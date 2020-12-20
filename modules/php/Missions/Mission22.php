<?php
namespace CREW\Missions;

class Mission22 extends AbstractMission
{
  public function __construct(){
    $this->id = 22;
    $this->desc = clienttranslate('You’re just barely out of the woods, when the temperature drops suddenly. All of the control systems alarms go off and you have to immediately start putting on your suits. The control module is struggling to keep up with the adjustments needed. Bypass the energy supply of the other modules successively in order to avoid a total blackout of the system.');
    $this->tasks = 5;
    $this->tiles = ['i1','i2','i3','i4'];
  }
}
