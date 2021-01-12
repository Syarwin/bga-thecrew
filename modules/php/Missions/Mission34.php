<?php
namespace CREW\Missions;
use \CREW\Game\Globals;

class Mission34 extends AbstractMission
{
  public function __construct(){
    $this->id = 34;
    $this->desc = clienttranslate('Neptune is already in sight as your ship begins to waver. Man the stabilizers so as not to lose control. In the meantime your commander must realign the gravitation module. <b>At no time may a crew member have won two tricks more than another crew member. In addition, your commander must win the first and last trick. </b>');
    $this->balanced = true;

    $this->informations = [
      'special' => clienttranslate('First and last trick'),
      'specialIcon' => 'commander',
      'specialTooltip' => clienttranslate('Your commander must win the first and last trick'),
    ];
  }

  function check($lastTrick)
  {
    // Balanced check
    parent::check($lastTrick);

    $firstOrLastTrick = Globals::getTrickCount() == 1 || Globals::isLastTrick();
    if(!$lastTrick['winner']->isCommander() && $firstOrLastTrick) {
      $this->fail();
    }
    else if(self::getStatus() == MISSION_CONTINUE && Globals::isLastTrick()) {
      $this->success();
    }
  }
}
