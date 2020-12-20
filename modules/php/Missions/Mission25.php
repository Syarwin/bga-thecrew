<?php
namespace CREW\Missions;

class Mission25 extends AbstractMission
{
  public function __construct(){
    $this->id = 25;
    $this->desc = clienttranslate('You’ve reached Saturn and pause to admire the magnificent spectacle of the rocky rings that constantly revolve around it. It is not without reason that they call it the ringed planet. Almost apathetically, you focus solely on the on-board analysis and collapse in exhaustion. Because of the dead zone, you won’t be disturbed. <b>If you are playing with five, you can now use the additional rule for five crew members.</b>');
    $this->tasks = 6;
    $this->tiles = ['i1', 'i2'];
    $this->deadzone = true;
    $this->special5 = true;
  }
}
