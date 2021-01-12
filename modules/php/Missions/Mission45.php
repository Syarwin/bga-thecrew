<?php
namespace CREW\Missions;

class Mission45 extends AbstractMission
{
  public function __construct(){
    $this->id = 45;
    $this->desc = clienttranslate('The effect is monumental! You are strapped into your seats but feel like you’re everywhere at the same time. Colors and shapes change, and light feels like a swirling mass that behaves like an intelligent being and ensnares you. You focus on your instruments and try not to lose your mind. ');
    $this->tasks = 9;
    $this->tiles = ['i1', 'i2', 'i3'];
  }
}
