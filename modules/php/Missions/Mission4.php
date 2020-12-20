<?php
namespace CREW\Missions;

class Mission4 extends AbstractMission
{
  public function __construct(){
    $this->id = 4;
    $this->desc = clienttranslate('You are nearing completion of the preparation phase. These last training phases are focused on the recalibration of the control modules, and the reorientation of the communicators and the advanced auxiliary systems on the spacesuits. You should be ready to launch soon. ');
    $this->tasks = 3;
  }
}
