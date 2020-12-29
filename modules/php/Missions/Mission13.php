<?php
namespace CREW\Missions;
use \CREW\Cards;

class Mission13 extends AbstractMission
{
  public function __construct(){
    $this->id = 13;
    $this->desc = clienttranslate('You have been hit by some small space debris despite having previously altered course. The control modules are indicating a malfunction in the drive. You will need to perform a thrust test on all drives to further isolate the problem. <b>You have to win a trick with each rocket card.</b>');
    $this->informations = [
      'cards' => clienttranslate('1 Trick with Each'),
      'cardsType' => 'rockets',
    ];
  }


  public function check($lastTrick)
  {
    // If at least one rocket, check that only one rockets is here
    if($lastTrick['bestCard']['color'] == CARD_ROCKET){
      foreach($lastTrick['cards'] as $card){
        if($card['color'] == CARD_ROCKET && $card['value'] != $lastTrick['bestCard']['value']){
          $this->fail();
          return;
        }
      }
    }

    // Otherwise, check if all rockets were played
    $rockets = Cards::getRemeaningRockets();
    if(empty($rockets)){
      $this->success();
    } else {
      $this->continue();
    }
  }
}
