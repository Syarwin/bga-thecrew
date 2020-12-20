<?php
namespace CREW\Missions;

class Mission15 extends AbstractMission
{
  public function __construct(){
    $this->id = 15;
    $this->desc = clienttranslate('You pass Mars and exit the dead zone. Your thoughts start to randomly drift to chocolate bars when suddenly your collision module sounds an alarm. Before you can even react appropriately, you are hit by a meteoroid. Immediately isolate the four damaged modules and begin making repairs.');
    $this->tasks = 4;
    $this->tiles = [1,2,3,4];
  }
}
