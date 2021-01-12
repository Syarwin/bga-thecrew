<?php
namespace CREW\Missions;
use \CREW\Game\Players;

class Mission29 extends AbstractMission
{
  public function __construct(){
    $this->id = 29;
    $this->desc = clienttranslate('Your communication module appears to have suffered more damage than you initially thought. The repair requires a series of tests and calibrations that you must carry out together and with precision. <b>At no time may a crew member have won two tricks more than another crew member. Communication is down. </b>');
    $this->deadzone = true;
    $this->balanced = true;
  }
}
