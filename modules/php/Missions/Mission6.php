<?php
namespace CREW\Missions;

class Mission6 extends AbstractMission
{
  public function __construct(){
    $this->id = 6;
    $this->desc = clienttranslate('After this minor setback, your final training phase lies ahead: Learning what to do in the case of restricted communication. In order to do so, a <b>dead zone</b> will be simulated, these can occur in space for a number of reasons, so it’s important to train for them. Strengthen your mental connections to pass the final tests. ');
    $this->tasks = 3;
    $this->tiles = ['i1','i2'];
    $this->deadzone = true;
  }
}
