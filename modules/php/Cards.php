<?php
namespace CREW;
use CREW\Game\Globals;
use CREW\Game\Notifications;
use CREW\Game\Players;

/*
 * Cards
 */
class Cards extends Helpers\Pieces
{
  protected static $table = "construction_cards";
	protected static $prefix = "card_";
  protected static $customFields = ['number', 'action'];
  protected static $autoreshuffleCustom = ['deck' => 'discard'];
  protected static $autoreshuffle = true;
  protected static function cast($card){
    return $card;
  }

  //////////////////////////////////
  //////////////////////////////////
  ///////////// SETUP //////////////
  //////////////////////////////////
  //////////////////////////////////

  public function setupNewGame($players, $options){
    $challenge = count($players) == 3 && $options[OPTION_CHALLENGE] == CHALLENGE_ON;
    /*
    $cards = [];
    foreach(self::$deck as $number => $nActions){
      foreach(self::$actions as $index => $action){
        $cards[] = [
          'number' => $number,
          'action' => $action,
          'nbr' => $nActions[$index]
        ];
      }
    }

    // Create cards
    $cards = array();
    foreach($this->colors as $color_id => $color) {
        if($color_id<6)
        {
            if($color_id == 2 && self::getGameStateValue( 'challenge') == 2) continue;

            for($value=1; $value<=($color_id == 5 ? 4 : 9); $value++) {

                if($color_id == 5 && $value== 1 && self::getGameStateValue( 'challenge') == 2) continue;

                $cards[] = array('type' => $color_id, 'type_arg' => $value, 'nbr' => 1);
            }
        }
    }

    $cards[] = array('type' => COMM, 'type_arg' => 0, 'nbr' => count($players));
    $this->cards->createCards($cards, 'deck');


    self::create($cards, 'deck');
    self::shuffle('deck');
    */
  }
}
