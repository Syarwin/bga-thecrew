<?php
namespace CREW\Missions;
use \CREW\Game\Globals;

class Mission33 extends AbstractMission
{
  public function __construct(){
    $this->id = 33;
    $this->desc = clienttranslate('A space walk is imminent! One of the hatches is broken and needs to be repaired. But leaving the ship always poses a risk. <b>After everyone has looked at his or her cards, your commander asks about his or her willingness to volunteer. It may only be answered with a “yes” or “no.” Your commander then selects another crew member. The chosen crew member must win exactly one trick, but not with a rocket card. </b>');

    $this->question = clienttranslate('Do you want to volunteer?');
    $this->replies = [clienttranslate('Yes'), clienttranslate('No') ];

    $this->informations = [
      'special' => clienttranslate('Exactly one trick'),
      'specialTooltip' => clienttranslate('This crew member must win exactly 1 trick, but not with a Rocket card'),
    ];
  }

  function check($lastTrick)
  {
    $player = $this->getSpecial();
    $n = $player->getTricksWon();
    $winWithRocket = $lastTrick['winner']->getId() == $player->getId() && $lastTrick['bestCard']['color'] == CARD_ROCKET;

    if($winWithRocket || $n > 1 || ($n == 0 && Globals::isLastTrick()) ){
      $this->fail();
    }
    else if(Globals::isLastTrick() && $n == 1) {
      $this->success();
    }
    else {
      $this->continue();
    }
  }
}
