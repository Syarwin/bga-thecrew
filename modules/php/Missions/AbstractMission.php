<?php
namespace CREW\Missions;
use CREW\Tasks;
use CREW\Game\Players;
use CREW\Game\Globals;
use CREW\Game\Notifications;
use thecrew;

abstract class AbstractMission
{
  protected $id = null;
  protected $desc = '';
  protected $tasks = 0;
  protected $tiles = [];
  protected $question = null;
  protected $replies = null;
  protected $deadzone = false;
  protected $disruption = 0;
  protected $informations = [];

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
    ];
  }

  public function getId(){ return $this->id; }
  public function getQuestion(){ return $this->question; }
  public function getReplies() { return $this->replies; }
  public function isDeadZone(){ return $this->deadzone; }
  public function canCommunicate($pId) { return true; }

  public function isDisrupted(){
    return $this->disruption > Globals::getTrickCount();
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
    if($this->question != null){
      return 'question';
    } else if($this->tasks > 0) {
      return 'task';
    } else {
      return 'trick';
    }
  }


  public function pickCrew($crewId)
  {
    $player = Players::getCurrent();
    $crew = Players::get($crewId);
    Globals::setSpecial($crewId);
    Notifications::specialCrewMember($player, $crew);
  }



  public function check($lastTrick){
    self::setStatus(Tasks::getStatus());
  }
  public function setStatus($p){
    Globals::setMissionFinished($p);
  }
  public function getStatus(){
    return Globals::getMissionFinished();
  }
  public function fail(){ self::setStatus(-1); }
  public function success(){ self::setStatus(1); }
  public function continue(){ self::setStatus(0); }

  // Utils
  protected function getSpecial(){
    return Players::get(Globals::getSpecial());
  }
}
