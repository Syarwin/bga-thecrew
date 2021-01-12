<?php
namespace CREW\Missions;

class Mission36 extends AbstractMission
{
  public function __construct(){
    $this->id = 36;
    $this->desc = clienttranslate('You take advantage of one of the rare moments of calm to socialize with each other. With all the minor and more major emergencies, the responsibility on your shoulders, and the uncertainty about the outcome of your adventure, tension has built up in every single crew member. So it’s good to just sit together, and talk. Relieved you re-dedicate yourselves to the coming challenges. <b>Your commander distributes the individual tasks.</b>');
    $this->tasks = 7;
    $this->tiles = [1, 2];
    $this->distribution = true;
  }
}
