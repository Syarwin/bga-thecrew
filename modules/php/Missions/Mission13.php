<?php
namespace CREW\Missions;

class Mission13 extends AbstractMission
{
  public function __construct(){
    $this->id = 13;
    $this->desc = clienttranslate('You have been hit by some small space debris despite having previously altered course. The control modules are indicating a malfunction in the drive. You will need to perform a thrust test on all drives to further isolate the problem. <b>You have to win a trick with each rocket card.</b>');
  }
}
