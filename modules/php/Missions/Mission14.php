<?php
namespace CREW\Missions;

class Mission14 extends AbstractMission
{
  public function __construct(){
    $this->id = 14;
    $this->desc = clienttranslate('You are now already close enough to Mars that you can see Olympus Mons, the highest volcano in our solar system, with the naked eye. You use this opportunity to first photograph it and then the Mars moons Phobos and Deimos. The sight makes up for the fact that right now you’re in a <b>dead zone</b>.');
    $this->tasks = 4;
    $this->tiles = ['i1','i2','i3'];
    $this->deadzone = true;
  }
}
