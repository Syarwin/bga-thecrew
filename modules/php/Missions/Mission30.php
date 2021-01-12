<?php
namespace CREW\Missions;

class Mission30 extends AbstractMission
{
  public function __construct(){
    $this->id = 30;
    $this->desc = clienttranslate('One part is done, but you postpone the rest of the repairs, as you are heading straight for Uranus. Its smooth, pale blue surface makes it look almost artificial. You tear yourself away from this fascinating view, because there are still repairs to be made. <b>You are only allowed to communicate from the second trick on.</b>');
    $this->tasks = 6;
    $this->tiles = ['i1', 'i2', 'i3'];
    $this->disruption = 2;
  }
}
