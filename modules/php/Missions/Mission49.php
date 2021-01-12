<?php
namespace CREW\Missions;

class Mission49 extends AbstractMission
{
  public function __construct(){
    $this->id = 49;
    $this->desc = clienttranslate('When you finally come to your senses, the situation has almost normalized. You just barely managed to take the necessary steps before you collapsed from exhaustion. Determining your current location you can hardly believe it. You are orbiting Venus! The wormhole is a direct link between Planet Nine and Venus, the second planet. This also explains the extreme heat, because Venus is significantly closer to the Sun than Earth. It takes a moment for the realization to dawn on you: You can go home! Check all ten main modules, focusing on life support, engines, and communication. Set course for Earth.');
    $this->tasks = 10;
    $this->tiles = ['i1','i2','i3'];
  }
}
