<?php
namespace CREW\Missions;
use \CREW\Cards;
use \CREW\Game\Globals;
use \CREW\Game\Players;

class Mission44 extends AbstractMission
{
  public function __construct(){
    $this->id = 44;
    $this->desc = clienttranslate('Up until now, wormholes have been at most theoretical constructs and now here you are, standing right in front of one. It overshadows you like a black monolith — incomprehensible, but with a huge attraction. You send a message to Earth and prepare the engines for the jump. <b>Every rocket card must win a trick. First the one rocket card, then the two, the three, and finally the four.</b>');
    $this->informations = [
      'cards' => clienttranslate('1 Trick with each ordered'),
      'cardsType' => 'rockets',
    ];
  }

  public function getUiData()
  {
      $data = parent::getUiData();
      if(Globals::isChallenge())
        $data['desc'] = clienttranslate('Up until now, wormholes have been at most theoretical constructs and now here you are, standing right in front of one. It overshadows you like a black monolith — incomprehensible, but with a huge attraction. You send a message to Earth and prepare the engines for the jump. <b>Every rocket card must win a trick. First the two rocket card, the three, and finally the four.</b>');
      return $data;
  }



  public function check($lastTrick)
  {
    // If at least one rocket, check that only one rockets is here
    if($lastTrick['bestCard']['color'] == CARD_ROCKET){
      // Another rocket ? => loose !
      foreach($lastTrick['cards'] as $card){
        if($card['color'] == CARD_ROCKET && $card['value'] != $lastTrick['bestCard']['value']){
          $this->fail();
          return;
        }
      }

      // Must be in order
      $expectedValue = Globals::getCheckCount();
      if(Players::count() == 3 && Globals::isChallenge()) $expectedValue++;

      if($expectedValue != $lastTrick['bestCard']['value'] - 1){
        $this->fail();
        return;
      }

      // Increment check count
      Globals::incCheckCount();
    }

    // Otherwise, check if all rockets were played
    $rockets = Cards::getRemeaningRockets();
    if($rockets->empty()){
      $this->success();
    } else {
      $this->continue();
    }
  }
}
