<?php
namespace CREW\Missions;

class Mission2 extends AbstractMission
{
  public function __construct(){
    $this->id = 2;
    $this->desc = clienttranslate('It is apparent that you are a perfectly matched crew. Above all, is your mental connection — this so-called drift compatibility bodes well for an ongoing successful collaboration. It’s time for training phases two and three: control technology and weightlessness.');
    $this->tasks = 2;
  }
}
