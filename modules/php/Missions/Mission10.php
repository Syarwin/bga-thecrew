<?php
namespace CREW\Missions;

class Mission10 extends AbstractMission
{
  public function __construct(){
    $this->id = 10;
    $this->desc = clienttranslate('After all this excitement right at the beginning of your mission you are now ready to leave the moon behind. You send your status back to Earth, activate the flight control and measurements instruments, and ignite the engines. This will truly be one giant leap. For you and for humankind.');
    $this->tasks = 4;
  }
}
