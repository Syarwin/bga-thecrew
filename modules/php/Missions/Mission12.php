<?php
namespace CREW\Missions;

class Mission12 extends AbstractMission
{
  public function __construct(){
    $this->id = 12;
    $this->desc = clienttranslate('You watch tensely as you pass remarkably close to the meteorites. In all of the excitement, you have messed up your records, causing a few minutes of confusion. <b>Immediately after the first trick, each of you must draw a random card from the crew member to your right and add it to your hand. Then continue playing normally.</b>');
    $this->tasks = 4;
    $this->tiles = ['o'];
  }
}
