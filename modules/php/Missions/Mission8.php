<?php
namespace CREW\Missions;

class Mission8 extends AbstractMission
{
  public function __construct(){
    $this->id = 8;
    $this->desc = clienttranslate('You have reached lunar orbit, weightlessness sets in — it’s an indescribable feeling. You’ve completed numerous tests and training for this moment and yet joy overwhelms you every time. You look back at Earth, which was your entire cosmos so far and already you can cover it with just your thumb. ');
    $this->tasks = 3;
    $this->tiles = [1,2,3];
  }
}
