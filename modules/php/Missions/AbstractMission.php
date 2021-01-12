<?php
namespace CREW\Missions;
use CREW\Tasks;
use CREW\Game\Players;
use CREW\Game\Globals;
use CREW\Game\Notifications;
use CREW\Helpers\Utils;
use thecrew;

abstract class AbstractMission
{
  protected $id = null;
  protected $desc = '';
  protected $tasks = 0;
  protected $hiddenTasks = false;
  protected $tiles = [];
  protected $question = null;
  protected $replies = null;
  protected $deadzone = false;
  protected $disruption = 0;
  protected $informations = [];
  protected $distribution = false;
  protected $balanced = false;

  public function getUiData()
  {
    return [
      'id' => $this->id,
      'desc' => $this->desc,
      'tasks' => $this->tasks,
      'tiles' => $this->tiles,
      'deadzone' => $this->deadzone,
      'informations' => $this->informations,
      'question' => $this->question,
      'replies' => $this->replies,
      'disruption' => $this->disruption,
      'hiddenTasks' => $this->hiddenTasks,
      'distribution' => $this->distribution,
      'balanced' => $this->balanced,
      'specialRule' => $this->isSpecialWithFivePlayers(),
    ];
  }

  public function getId(){ return $this->id; }
  public function getQuestion(){
    return $this->distribution? clienttranslate('Do you want to take the task?') : $this->question;
  }
  public function getReplies() {
    return $this->distribution?  [clienttranslate('Yes'), clienttranslate('No') ] : $this->replies;
  }
  public function isDeadZone(){ return $this->deadzone; }
  public function canCommunicate($pId) { return true; }
  public function areTasksHidden(){ return $this->hiddenTasks || $this->distribution; }
  public function isSpecialWithFivePlayers(){
    return $this->id >= 25 && $this->tasks > 0;
  }

  public function isDisrupted(){
    return $this->disruption > Globals::getTrickCount();
  }

  public function getTargetablePlayers($removeCommander = true){
    if($this->distribution)
      return $this->getTargetablePlayersForDistribution();

    $playerIds = Players::getAll()->getIds();
    if($removeCommander){
      Utils::diff($playerIds, [Globals::getCommander()]);
    }
    return $playerIds;
  }


  /*
   * Enforce an evenly distribution
   */
  public function getTargetablePlayersForDistribution(){
    // Compute how much task each player should have at the end
    $players = Players::getAll();
    $quotient = intdiv($this->tasks, $players->count());
    $remainder = $this->tasks % $players->count();

    // Compute the numer of player already having the max number of tasks
    $nPlayersAtMax = $players->reduce(function($carry, $player) use ($quotient){
      return $carry + ($player->countTasks() > $quotient? 1 : 0);
    }, 0);


    $result = [];
    foreach(Players::getAll() as $pId => $player){
      $nTasks = $player->countTasks();
      if($nTasks < $quotient || ($nTasks == $quotient && $remainder > $nPlayersAtMax))
        $result[] = $pId;
    }

    return $result;
  }


  /*
   * Return the list of task, that might be hidden depending on the mission
   */
  public function getTasks($tasks){
    if($this->areTasksHidden()){
      Tasks::hide($tasks, $this->distribution);
    }
    return $tasks;
  }


  public function prepare()
  {
    // Create tasks
    Tasks::addMany($this->tasks, $this->tiles);

    // Notify question if needed
    if (!is_null($this->question)){
      Notifications::message(clienttranslate('Commander ${player_name} asks : ${question}'), [
        'i18n' => ['question'],
        'player_name' => Players::getCommander()->getName(),
        'question' => $this->question,
      ]);
    }
  }


  public function getStartingState()
  {
    if($this->question != null || $this->distribution){
      return 'question';
    } else if($this->tasks > 0) {
      return 'task';
    } else {
      return 'trick';
    }
  }


  public function pickCrew($crewId)
  {
    $crew = Players::get($crewId);

    // Distribution => assign next task
    if($this->distribution){
      $taskId = Tasks::getUnassignedIds()[0];
      $task = Tasks::assign($taskId, $crew);
      Notifications::assignTask($task, $crew);

      return count(Tasks::getUnassigned()) > 0? 'next' : 'trick';
    }
    // Tasks hidden => the commander assign all of them to someone
    else if($this->hiddenTasks){
      foreach(Tasks::getUnassignedIds() as $taskId){
        // Assign task and notify
        $task = Tasks::assign($taskId, $crew);
        Notifications::assignTask($task, $crew);
      }
    }
    // Otherwise, generic action is to declare crew member as special
    else {
      $player = Players::getCurrent();
      Globals::setSpecial($crewId);
      Notifications::specialCrewMember($player, $crew);
    }

    return null;
  }



  public function check($lastTrick){
    self::setStatus(Tasks::getStatus());

    if($this->balanced){
      $players = Players::getAll();
      $min = $players->reduce(function($min, $player){ return min($min, $player->getTricksWon()); }, 100);
      $max = $players->reduce(function($max, $player){ return max($max, $player->getTricksWon()); }, 0);

      $status = $min + 2 < $max? MISSION_FAIL : (Globals::isLastTrick()? MISSION_SUCCESS : MISSION_CONTINUE);
      $this->setStatus($status);
    }
  }

  public function setStatus($p){
    Globals::setMissionFinished($p);
  }
  public function getStatus(){
    return Globals::getMissionFinished();
  }
  public function fail(){ self::setStatus(MISSION_FAIL); }
  public function success(){ self::setStatus(MISSION_SUCCESS); }
  public function continue(){ self::setStatus(MISSION_CONTINUE); }

  // Utils
  protected function getSpecial(){
    return Players::get(Globals::getSpecial());
  }
}
