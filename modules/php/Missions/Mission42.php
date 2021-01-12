<?php
namespace CREW\Missions;

class Mission42 extends AbstractMission
{
  public function __construct(){
    $this->id = 42;
    $this->desc = clienttranslate('The planet is extremely cold and inhospitable but seems to be habitable. During your investigation you notice an area that appears to be moving away from your instruments. The closer you get to this anomaly, the more blatant the measurement errors become. What this means defies scientific explanation. At least you can narrow in on the phenomenon, because the results normalize when you move away.');
    $this->tasks = 9;
  }
}
