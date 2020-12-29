<?php
namespace CREW\Missions;
use CREW\Game\Globals;

class Mission9 extends AbstractMission
{
  public function __construct(){
    $this->id = 9;
    $this->desc = clienttranslate('You are abruptly torn from your thoughts, as the onboard analysis module NAVI deafeningly sounds an alarm, demanding your attention. A tiny piece of metal has become wedged in the electronic drive unit. A steady hand will be needed in order to not damage the circuit boards. <b>At least one color card with a value of 1 must win a trick.</b>');
    $this->informations = [
      'cards' => clienttranslate('1 Trick with'),
      'cardsType' => 'ones',
    ];
  }


  function check($lastTrick)
  {
    $card = $lastTrick['bestCard'];
    if($card['value'] == 1 && $card['color'] != CARD_ROCKET){
      $this->success();
    }
    else if(Globals::isLastTrick()) {
      $this->fail();
    }
    else {
      $this->continue();
    }
  }
}
