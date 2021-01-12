<?php
namespace CREW\Missions;
use \CREW\Game\Globals;
use \CREW\Cards;

class Mission26 extends AbstractMission
{
  public function __construct(){
    $this->id = 26;
    $this->desc = clienttranslate('A loud bang interrupts your introspective mood. Two space rocks that were in the vicinity of Saturn have torn holes in your ship’s hull. The on-board analysis module has immediately sealed off the affected storage area. Both rocks are still stuck in the shell of the hull. You must carefully remove them without further increasing the damage done. <b>At least two color cards, each with a value of one, must win one trick each.</b>');

    $this->informations = [
      'cards' => clienttranslate('2 Trick with'),
      'cardsType' => 'ones',
    ];
  }


  function check($lastTrick)
  {
    $card = $lastTrick['bestCard'];
    if($card['value'] == 1 && $card['color'] != CARD_ROCKET){
      Globals::incCheckCount();
    }

    $ones = Cards::getRemeaningOfValue(1)->count();
    $n = Globals::getCheckCount();
    if($n >= 2){
      $this->success();
    }
    else if(Globals::isLastTrick() || $ones + $n < 2){
      $this->fail();
    }
    else {
      $this->continue();
    }
  }
}
