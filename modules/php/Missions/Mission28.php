<?php
namespace CREW\Missions;

class Mission28 extends AbstractMission
{
  public function __construct(){
    $this->id = 28;
    $this->desc = clienttranslate('Readings are indicating an unusually high magnitude of cosmic rays in the area. You continue your flight oblivious, uninterrupted, and unaware that your radio messages are either not transmitting, or arriving with a great deal of time delay. This is not going to make working easy. <b>You are only allowed to communicate from the third trick on.</b>');
    $this->tasks = 6;
    $this->tiles = [1, 'o'];
    $this->disruption = 3;
  }
}
