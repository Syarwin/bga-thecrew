<?php
namespace CREW\Missions;
use \CREW\Game\Globals;
use \CREW\Game\Players;
use \CREW\Game\Notifications;

class Mission11 extends AbstractMission
{
  public function __construct(){
    $this->id = 11;
    $this->desc = clienttranslate('Your radar systems report a dense meteorite field that will soon intercept your course. <b>The commander designates a crew member who must complete the recalculation of the course. This task will require a high level of concentration, thus the chosen crew member must not communicate during this part of the mission. </b>');
    $this->tasks = 4;
    $this->tiles = [1];
    $this->informations = [
      'special' => clienttranslate("Can't speak"),
      'specialTooltip' => clienttranslate('This crew member cannot communicate in this mission.'),
    ];
  }


  public function getStartingState()
  {
    return 'pickCrew';
  }

  public function pickCrew($crewId)
  {
    parent::pickCrew($crewId);
    $player = Players::get($crewId);
    $player->usedComm();
    Notifications::communicate($player, null, 'used');
  }
}
