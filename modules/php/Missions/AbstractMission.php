<?php
namespace CREW\Missions;
use CREW\Tasks;
use CREW\Game\Players;
use thecrew;

abstract class AbstractMission
{
  protected $id = null;
  protected $desc = '';
  protected $tasks = 0;
  protected $tiles = [];
  protected $question = null;

  public function getUiData()
  {
    return [
      'id' => $this->id,
      'desc' => $this->desc,
    ];
  }

  public function getId(){ return $this->id; }


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
}
