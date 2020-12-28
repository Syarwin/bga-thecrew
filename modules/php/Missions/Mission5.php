<?php
namespace CREW\Missions;
use \CREW\Game\Globals;

class Mission5 extends AbstractMission
{
  public function __construct(){
    $this->id = 5;
    $this->desc = clienttranslate('Celebrated too soon! One of you is sick in bed. <b>After everyone has looked at his or her cards, your commander asks everyone how he or she feels. It may only be answered with “good” or “bad.” Your commander then decides who is ill. The sick crew member must not win any tricks.</b>');

    $this->question = clienttranslate('How do you feel?');
    $this->replies = [clienttranslate('Good'), clienttranslate('Bad') ];
  }


  function check()
  {
    $player = $this->getSpecial();
    if($player->getTricksWon() > 0){
      $this->fail();
    }
    else if(Globals::isLastTrick()) {
      $this->success();
    }
    else {
      $this->continue();
    }
  }
}
