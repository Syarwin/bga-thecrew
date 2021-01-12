<?php
namespace CREW\Missions;
use \CREW\Game\Globals;

class Mission41 extends AbstractMission
{
  public function __construct(){
    $this->id = 41;
    $this->desc = clienttranslate('You regulate the engines to prepare for landing. Due to the completely new conditions, one of you has to take over the coordination of the landing. <b>After everyone has looked at his or her hand, your commander asks everyone about his or her readiness. It may only be answered with a “yes” or “no.” Your commander then selects another crew member for the task. This crew member’s task is to only win the first and last tricks. Since only the thrusters are used for correcting the position, neither trick may be won with rocket cards.</b>');


    $this->question = clienttranslate('Are you ready?');
    $this->replies = [clienttranslate('Yes'), clienttranslate('No') ];

    $this->informations = [
      'special' => clienttranslate('Only 1st and Last Trick'),
      'specialTooltip' => clienttranslate('This crew member’s must win the first and last tricks, without any rocket card'),
    ];
  }

  function check($lastTrick)
  {
    $player = $this->getSpecial();
    $firstOrLastTrick = Globals::getTrickCount() == 1 || Globals::isLastTrick();

    // Last trick was won by special crew member
    if($lastTrick['winner']->isSpecial()){
      // It must be first or last, and not with a rocket
      if(!$firstOrLastTrick || $lastTrick['bestCard']['color'] == CARD_ROCKET){
        $this->fail();
        return;
      }
    }
    // Someone else won the trick ? => must not be first or last
    else if($firstOrLastTrick){
      $this->fail();
      return;
    }

    // Last trick => special member must have won exactly 2 tricks
    if(Globals::isLastTrick()){
      if($player->getTricksWon() == 2)
        $this->success();
      else
        $this->fail();
    }
    else {
      $this->continue();
    }
  }
}
