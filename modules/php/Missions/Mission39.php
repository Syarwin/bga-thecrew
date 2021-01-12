<?php
namespace CREW\Missions;

class Mission39 extends AbstractMission
{
  public function __construct(){
    $this->id = 39;
    $this->desc = clienttranslate('This has to be it! The readings indicated on your modules can only be produced by a truly gigantic object. The effects are so massive that even your <b>radio communication is interrupted</b>. Recalibrate your instruments and find out what’s really going on. ');
    $this->tasks = 8;
    $this->tiles = ['i1', 'i2', 'i3'];
    $this->deadzone = true;
  }
}
