<?php
namespace CREW\Missions;
use CREW\Tasks;
use CREW\Game\Players;
use CREW\Game\Globals;
use thecrew;

abstract class AbstractMission
{
  protected $id = null;
  protected $desc = '';
  protected $tasks = 0;
  protected $tiles = [];
  protected $question = null;
  protected $disruption = 0;

  public function getUiData()
  {
    return [
      'id' => $this->id,
      'desc' => $this->desc,
      'tasks' => $this->tasks,
      'tiles' => $this->tiles,
    ];
  }

  public function getId(){ return $this->id; }

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
      thecrew::get()->activeNextPlayer();
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


  public function check()
  {
    if(thecrew::getUniqueValueFromDB("select count(*) from task")>0 // NOI18N
        && thecrew::getUniqueValueFromDB("select count(*) from task where status <> 'ok'") == 0)// NOI18N
    {
        $this->missionSuccess();
    }
    else if(thecrew::getUniqueValueFromDB("select count(*) from task where status = 'nok'") > 0// NOI18N
        || $this->isLastTrick())
    {
        $this->missionFailed();
    }
    else
    {
        //otherwise we continue
        $this->thecrew->setGameStateValue( 'mission_finished', 0 );
    }

  }
}
