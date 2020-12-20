<?php
namespace CREW\Missions;

class Mission7 extends AbstractMission
{
  public function __construct(){
    $this->id = 7;
    $this->desc = clienttranslate('This will be a memorable day! You are now prepared for launch. The completion of training, however, is only just the beginning of your adventure. 10-9-8-7-6-5-4-3-2-1-LIFT OFF! A massive force presses you into your seats — now there is no going back. With a deafening noise, you leave the ground, the country, the continent, and the planet. ');
    $this->tasks = 3;
    $this->tiles = ['o'];
  }
}
