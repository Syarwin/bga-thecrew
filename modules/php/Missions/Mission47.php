<?php
namespace CREW\Missions;

class Mission47 extends AbstractMission
{
  public function __construct(){
    $this->id = 47;
    $this->desc = clienttranslate('You are exhausted, at the end of your rope. The jump feels like a jail now, in which you can no longer distinguish between reality and fantasy. Your body screams that you can barely stand even ten more seconds of this, but your mind questions how long ten seconds actually is. You begin to count them down — and suddenly burst out of the wormhole.');
    $this->tasks = 10;
  }
}
