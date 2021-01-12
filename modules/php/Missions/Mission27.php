<?php
namespace CREW\Missions;

class Mission27 extends AbstractMission
{
  public function __construct(){
    $this->id = 27;
    $this->desc = clienttranslate('You determine that the tear in the hull was not without consequences. A review of the modules associated with the area indicates damage to the flux capacitor. Although currently this is not a major problem, repairs will be necessary in the long-run, if you want to make it home. <b>Your commander specifies who should carry out the repair.</b>');
    $this->tasks = 3;
    $this->hiddenTasks = true;

    $this->question = clienttranslate('Are you OK to take the tasks?');
    $this->replies = [clienttranslate('Yes'), clienttranslate('No') ];
  }
}
