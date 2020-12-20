<?php
namespace CREW\Missions;

class Mission16 extends AbstractMission
{
  public function __construct(){
    $this->id = 16;
    $this->desc = clienttranslate('Overall, the shock was worse than the actual damage, and you were able to fix most of it in a timely manner. However, the ninth control module, which monitors your suits’ life support systems, has been severely damaged in the collision and has failed. <b>You must not win a trick with a nine.</b>');
  }
}
