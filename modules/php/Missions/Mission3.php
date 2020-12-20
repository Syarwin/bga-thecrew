<?php
namespace CREW\Missions;

class Mission3 extends AbstractMission
{
  public function __construct(){
    $this->id = 3;
    $this->desc = clienttranslate('The training phases each build on the lessons learned in the previous phase. This combined energy supply and emergency prioritization course will require a high degree of logical thinking to understand and make the appropriate connections. Your education in mathematics will certainly come in handy for this.');
    $this->tasks = 2;
    $this->tiles= [1,2];
  }
}
