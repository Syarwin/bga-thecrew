<?php
namespace CREW\Missions;

class Mission5 extends AbstractMission
{
  public function __construct(){
    $this->id = 5;
    $this->desc = clienttranslate('Celebrated too soon! One of you is sick in bed. <b>After everyone has looked at his or her cards, your commander asks everyone how he or she feels. It may only be answered with “good” or “bad.” Your commander then decides who is ill. The sick crew member must not win any tricks.</b>');

    // TODO
    $this->question =  clienttranslate('How do you feel?');
    $this->replies = clienttranslate('Good/Bad');
  }
}
