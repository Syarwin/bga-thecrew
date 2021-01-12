<?php
namespace CREW\Missions;

class Mission31 extends AbstractMission
{
  public function __construct(){
    $this->id = 31;
    $this->desc = clienttranslate('As you slowly move away from Uranus, you receive a message from Earth requesting the collection of metadata from the moons of Uranus. Unfortunately, due to the radio interference, it arrived too late, you can only see three of the 27 moons — Rosalind, Belinda, and Puck. That will have to suffice for the time being.');
    $this->tasks = 6;
    $this->tiles = ['1', '2', '3'];
  }
}
