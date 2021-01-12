<?php
namespace CREW\Missions;

class Mission43 extends AbstractMission
{
  public function __construct(){
    $this->id = 43;
    $this->desc = clienttranslate('In the name of science, you venture closer. The gravitational laws seem to invert the closer you get to the anomaly and you need to anchor yourself to the planet’s surface using vibranium hooks for safety. <b>Your commander secures the rest of the crew and distributes the individual tasks.</b> The data you are collecting allows for only one conclusion: You have discovered a wormhole.');
    $this->tasks = 9;
    $this->distribution = true;
  }
}
