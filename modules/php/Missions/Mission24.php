<?php
namespace CREW\Missions;

class Mission24 extends AbstractMission
{
  public function __construct(){
    $this->id = 24;
    $this->desc = clienttranslate('The incident has made a mess of your normal processes. There is a lot to do at the moment and none of you know where to start. Your commander takes the initiative and devises a plan in order to proceed in a organized manner. <b>Once everyone has looked at their cards, the commander asks each crew member about their willingness to take on the task. This may only be answered “yes” or “no.” The commander distributes the tasks.<br/>6 tasks</b>');
    $this->tasks = 6;
    $this->distribution = true;

    //$this->questions =  'question' => clienttranslate('Do you want to take the task?'), 'replies' => clienttranslate('Yes/No')
  }
}
