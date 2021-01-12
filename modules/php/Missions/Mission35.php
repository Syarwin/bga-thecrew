<?php
namespace CREW\Missions;

class Mission35 extends AbstractMission
{
  public function __construct(){
    $this->id = 35;
    $this->desc = clienttranslate('Cautiously you reach the outermost planet of our solar system: the ice giant Neptune. Its deep blue makes you shiver. As you slowly pass Neptune, you receive another message from Earth. The space probe Alpha 5 is orbiting Neptune, but has damaged sensors. Find it and fix them. ');
    $this->tasks = 7;
    $this->tiles = ['i1', 'i2', 'i3'];
  }
}
