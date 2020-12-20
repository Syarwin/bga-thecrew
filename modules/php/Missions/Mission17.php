<?php
namespace CREW\Missions;

class Mission17 extends AbstractMission
{
  public function __construct(){
    $this->id = 17;
    $this->desc = clienttranslate('The damage to the ninth control module is even worse than originally thought, so you will have to invest significantly more time in making the repair. At the same time, however, you still have to track your course and send a message back to Earth where they are eagerly awaiting your status. <b>You still must not win a trick with a nine.</b>');
  }
}
