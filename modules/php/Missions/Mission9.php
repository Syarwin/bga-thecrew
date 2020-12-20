<?php
namespace CREW\Missions;

class Mission9 extends AbstractMission
{
  public function __construct(){
    $this->id = 9;
    $this->desc = clienttranslate('You are abruptly torn from your thoughts, as the onboard analysis module NAVI deafeningly sounds an alarm, demanding your attention. A tiny piece of metal has become wedged in the electronic drive unit. A steady hand will be needed in order to not damage the circuit boards. <b>At least one color card with a value of 1 must win a trick.</b>');
  }
}
