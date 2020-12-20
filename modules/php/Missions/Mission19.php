<?php
namespace CREW\Missions;

class Mission19 extends AbstractMission
{
  public function __construct(){
    $this->id = 19;
    $this->desc = clienttranslate('The dust cloud is surprising in magnitude and the longer you are in it, the stranger your communication module reacts. It fluctuates between being as clear as glass to being completely incomprehensible. However, the severely impaired time periods are becoming longer. <b>You are only allowed to communicate from the third trick on. </b>');
    $this->tasks = 5;
    $this->tiles = [1];
    $this->disruption = 3;
  }
}
