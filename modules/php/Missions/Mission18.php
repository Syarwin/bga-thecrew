<?php
namespace CREW\Missions;

class Mission18 extends AbstractMission
{
  public function __construct(){
    $this->id = 18;
    $this->desc = clienttranslate('You set course for Jupiter, as you fly into a dust cloud. At almost the same time there is something strange going on with your communication module. It initially indicates there’s an astonishingly good connection, but only seconds later it appears to be in a total blackout. <b>You are only allowed to communicate from the second trick on. </b>');
    $this->tasks = 5;
    $this->disruption = 2;
  }
}
