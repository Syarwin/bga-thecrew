<?php
namespace CREW\Missions;

class Mission21 extends AbstractMission
{
  public function __construct(){
    $this->id = 21;
    $this->desc = clienttranslate('After making the repairs you ascertain that you were clearly too close to Jupiter while you were passing through the cloud. Its gravitational force of around two and a half times that of Earth has drastically affected your course. In order to counteract the effect, you will have concentrate and work in a precise manner to reach the ideal exit point. During this, you hardly notice the <b>faulty radio communication.</b>');
    $this->tasks = 5;
    $this->tiles = [1,2];
    $this->deadzone = true;
  }
}
