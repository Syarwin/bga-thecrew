<?php
namespace CREW\Missions;

class Mission38 extends AbstractMission
{
  public function __construct(){
    $this->id = 38;
    $this->desc = clienttranslate('You reach the heliopause, the border area of our solar system. If the calculations prove correct, the ninth planet cannot be far away. A feeling of excitement spreads, the hour of truth approaches. As your instruments react, you almost jump out of your seats. But unfortunately it is just a false alarm. <b>You are only allowed to communicate from the third trick on. </b>');
    $this->tasks = 8;
    $this->disruption = 3;
  }
}
