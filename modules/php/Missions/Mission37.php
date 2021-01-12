<?php
namespace CREW\Missions;

class Mission37 extends AbstractMission
{
  public function __construct(){
    $this->id = 37;
    $this->desc = clienttranslate('You reach the dwarf planet Pluto. Many years ago it would have been the ninth planet. You take a moment to reminisce about how your very educated mother used to serve you noodles, and talk to you about the planets while you reflect on the changeability of things. Nevertheless, the ship must be kept on course. <b>The commander decides who should take care of it.</b>');
    $this->tasks = 4;
    $this->hiddenTasks = true;

    $this->question = clienttranslate('Are you OK to take the tasks?');
    $this->replies = [clienttranslate('Yes'), clienttranslate('No') ];
  }
}
