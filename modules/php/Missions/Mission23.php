<?php
namespace CREW\Missions;

class Mission23 extends AbstractMission
{
  public function __construct(){
    $this->id = 23;
    $this->desc = clienttranslate('Most of the modules are still on emergency back-up supply while you puzzle over the cause of the rapid cooling. Callisto, one of the 79 moons of Jupiter, passes by, the moment you appear to escape the frost field. <b>Before you select the task cards, you can swap the position of two task tokens. Decide together but do not reveal anything about your own cards.</b>');
    $this->tasks = 5;
    $this->tiles = [1,2,3,4,5];

    //$this->questions =  'question' => clienttranslate('Do you want to take the task?'), 'replies' => clienttranslate('Yes/No')
  }

  public function getStartingState()
  {
    return 'multiSelect';
  }

}
