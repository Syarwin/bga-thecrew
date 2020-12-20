<?php
namespace CREW;
use CREW\Game\Globals;

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
      'color' => (int) $row['card_type'],
      'value' => (int) $row['card_type_arg'],
      'tile' => $row['token'],
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

  public static function insert($id, $color, $value, $tile)
  {
    self::DB()->insert([
      'task_id' => $id,
      'card_type' => $color,
      'card_type_arg' => $value,
      'token' => $tile,
    ]);
  }


  /*
   * Add several tasks with corresponding tiles
   */
  public static function addMany($n, $tiles = [])
  {
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
}
