<?php
namespace CREW\Missions;
use \CREW\Game\Globals;
use \CREW\Game\Notifications;
use \CREW\Game\Players;


class Mission51 extends AbstractMission
{
  public function __construct(){
    $this->id = 51;
    $this->desc = clienttranslate('You can now restart a new campaign.');
  }
}
