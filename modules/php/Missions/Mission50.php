<?php
namespace CREW\Missions;

class Mission50 extends AbstractMission
{
  public function __construct(){
    $this->id = 50;
    $this->desc = clienttranslate('The way back is much more complicated than expected. Some modules have been permanently damaged during the trip and you will have to fight against the immense gravitational pull of the Sun. With your reserves depleted, you cannot allow yourself any mistakes — the voyage home must be executed precisely. You must first take advantage of a gravitational catapult. Then, the ship’s modules must be kept under control and the approach to Earth initiated. In the end, landing on Earth will require no less attention than any other maneuver you’ve performed so far. <b>Everyone looks at his or her cards. A crew member must win the first four tricks. Another crew member has to win the last trick. The remaining crew members must win all the tricks in between. Your commander asks everyone for his or her preferred task, you then decide together as a crew who should assume which role. Do not say anything about your cards. </b>');

    //question' => clienttranslate('What is your preferred task?'), 'replies' => clienttranslate('Only the first four tricks/All tricks in between/Only the last trick'))
  }
}
