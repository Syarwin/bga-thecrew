<?php
namespace CREW\Missions;

class Mission32 extends AbstractMission
{
  public function __construct(){
    $this->id = 32;
    $this->desc = clienttranslate('In the meantime, despite the favorable conditions, it is obvious how long you have been on the road together and the inevitable human characteristics begin to come to light. In order to avoid a heated conflict, everyone delves into his or her work. <b>Your commander takes over the organization and distributes the individual tasks.</b> ');
    $this->tasks = 7;
    $this->distribution = true;
  }
}
