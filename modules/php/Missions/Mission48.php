<?php
namespace CREW\Missions;
use \CREW\Game\Globals;

class Mission48 extends AbstractMission
{
  public function __construct(){
    $this->id = 48;
    $this->desc = clienttranslate('You barely have time to orient yourself. It is suddenly extremely hot everywhere. The on-board analysis module instantly shifts the entire ship into the highest danger level. The first of the modules begin to fall victim to the radical temperature fluctuations. Even in your regulated suits, you break into a sweat within seconds. You need to activate the emergency protocol, extend the heat shields, and get away from the heat source as quickly as possible. <b>The Omega task must be won in the last trick. </b>');
    $this->tasks = 3;
    $this->tiles = ['o'];
  }

  function check($lastTrick)
  {
    // Check tasks
    parent::check($lastTrick);

    // The tasks must be all satisfied only if that was the last trick
    if(self::getStatus() == MISSION_SUCCESS && !Globals::isLastTrick()){
      $this->fail();
    }
  }
}
