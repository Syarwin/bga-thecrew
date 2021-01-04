<?php
namespace CREW;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Game\Notifications;

/*
 * Tasks: a class that handles tasks
 */
class Tasks extends \CREW\Helpers\DB_Manager
{
  protected static $table = 'task';
  protected static $primary = "task_id";
  protected static $associative = false;
  protected static function cast($row)
  {
    return [
      'id' => (int) $row['task_id'],
      'color' => (int) $row['color'],
      'value' => (int) $row['value'],
      'tile' => $row['tile'],
      'pId' => $row['player_id'],
      'status' => $row['status'],

/*
      `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `card_type` varchar(16) NOT NULL COMMENT 'Color of the card: 1 => blue, 2 => green, 3 => pink, 4 => yellow, 5 => Rocket, 6 => reminder',
      `card_type_arg` int(11) NOT NULL COMMENT 'Value of the card. Numeric value',
      `token` varchar(3) NOT NULL DEFAULT '' COMMENT '',
      `player_id` int(11) NULL COMMENT 'The id of the owner if it means something',
      `status` varchar(3) NOT NULL DEFAULT 'tbd' COMMENT 'tbd, nok, ok',
      `trick` int(5) NULL COMMENT 'trick number where task has been done',
*/
    ];
  }

  public static function get($taskId)
  {
    return self::DB()->where($taskId)->get(true);
  }

  public static function count()
  {
    return self::DB()->count();
  }

  public static function insert($id, $color, $value, $tile)
  {
    self::DB()->insert([
      'task_id' => $id,
      'color' => $color,
      'value' => $value,
      'tile' => $tile,
    ]);
  }


  /*
   * Add several tasks with corresponding tiles
   */
  public static function addMany($n, $tiles = [])
  {
    if($n == 0)
      return;

    $lastTaskId = self::DB()->count();

    // Create possible tasks depending on challenge mode
    $tasks = [];
    for($color = 1; $color <= 4; $color++){
      if($color == CARD_GREEN && Globals::isChallenge())
        continue;

      for($i = 1; $i <= 9; $i++){
        $tasks[] = [$color, $i];
      }
    }

    // Remove already existing tasks
    $dbTasks = self::DB()->get(false)->map(function($task){ return [$task['color'], $task['value'] ]; });
    $tasks = array_diff($tasks, $dbTasks);


    // Pick n random elements
    $picked = (array) array_rand($tasks, $n);

    // Insert in DB
    foreach($picked as $i => $key){
      $task = $tasks[$key];
      $tile = $tiles[$i] ?? '';
      self::insert($lastTaskId + $i + 1, $task[0], $task[1], $tile);
    }
  }

  public static function add($tile = '')
  {
    self::addMany(1, [$tile]);
  }


  /*
   * Get the tasks that still need to be assigned
   */
  public static function getUnassigned()
  {
    return self::DB()->whereNull('player_id')->get(false)->toArray();
  }

  public static function getUnassignedIds()
  {
    return self::DB()->whereNull('player_id')->get(false)->getIds();
  }


  /*
   * Assign a task to a player
   */
  public static function assign($taskId, $player)
  {
    self::DB()->update(['player_id' => $player->getId() ])->run($taskId);
    return self::get($taskId);
  }


  /*
   * Get tasks of a player
   */
  public static function getOfPlayer($pId)
  {
    return self::DB()->where('player_id', $pId)->get(false)->toArray();
  }



  /*
   * Remove all tasks from previous mission in DB
   */
  public static function clearMission()
  {
    self::DB()->delete()->run();
  }



  public static function getRemeaning()
  {
    return self::DB()->where('status','tbd')->get(false)->toArray();
  }


  /*
   * checkLastTrick : called at the end of a trick
   */
  public static function checkLastTrick()
  {
    $tasks = self::getRemeaning();
    $winner = Globals::getLastWinner();
    $cards = Cards::getLastTrick();

    //update task individually
    foreach($tasks as $task){
      foreach($cards as $card){
        if($task['color'] == $card['color'] && $task['value'] == $card['value']){
          $assignedPlayer = Players::get($task['pId']);
          self::updateStatus([$task], $task['pId'] == $winner->getId());
        }
      }
    }


    // Tile 1 -> 5
    for($i = 1; $i <= 5; $i++){
      $task = self::DB()->where('tile', $i)->where('status', 'tbd')->get(true);
      if(is_null($task))
        continue;

      $nTasksBefore = self::DB()->whereNotIn('tile', range(1,$i-1))->where('status', 'ok')->count();
      if($nTasksBefore > 0){
        self::updateStatus([$task]);
      }
    }


    //Update task according to tile > >> >>> >>>>
    $tasks = self::DB()->where('tile', 'LIKE', 'i%')->where('status', 'ok')->orderBy('tile')->get(false);
    foreach($tasks as $task) {
      $taskMissed = self::DB()->where('tile', 'LIKE', 'i%')->where('tile', '<', $task['tile'])->where('status', 'tbd')->get(false);
      self::updateStatus($tasksMissed);
    }

    //Update task according to tile Omega
    $task = self::DB()->where('tile', 'o')->where('status', 'ok')->get(true);
    if(!is_null($task)){
      $tasksMissed = self::DB()->where('status', 'tbd')->get(false);
      self::updateStatus($tasksMissed);
    }
  }


  function updateStatus($tasksMissed, $success = false)
  {
    foreach($tasksMissed as $task){
      $assignedPlayer = Players::get($task['pId']);
      $task['status'] = $success? 'ok' : 'nok';

      // Update status and notify
      self::DB()->update([
        'trick' => Globals::getTrickCount(),
        'status' => $task['status'],
      ], $task['id']);
      Notifications::updateTaskStatus($task, $assignedPlayer);
    }
  }


  /*
   * getStatus : return
   *   * 1 if all tasks satisfied
   *   * -1 if a task failed or a task is still tbd but mission is over
   *   * 0 otherwise
   */
  public static function getStatus()
  {
    if(self::count() > 0 && self::DB()->where('status', '<>', 'ok')->count() == 0)
      return 1;

    if(self::DB()->where('status', '=', 'nok')->count() > 0 || Globals::isLastTrick() )
      return -1;

    return 0;
  }


  function hide(&$tasks)
  {
    foreach($tasks as &$task){
      $task['value'] = 0;
      $task['color'] = CARD_HIDDEN;
    }
  }


  /*
   * Usefull in mission 23 to switch tiles
   */
  function setTile($taskId, $newTile)
  {
    self::DB()->update(['tile' => $newTile], $taskId);
  }
}
