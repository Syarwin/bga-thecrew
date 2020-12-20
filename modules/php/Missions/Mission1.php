<?php
namespace CREW\Missions;

class Mission1 extends AbstractMission
{
  public function __construct(){
    $this->id = 1;
    $this->desc = clienttranslate('Congratulations! You have been chosen from a vast array of applicants to participate in the most important, and dangerous adventure that mankind has ever faced: the search for Planet Nine. You barely arrive at the training facility before you have already begun your first training phase: team building.');
    $this->tasks = 1;
  }
}
