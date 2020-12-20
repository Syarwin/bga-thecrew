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
  protected static $table = "card";
	protected static $prefix = "card_";
  protected static $customFields = ['value', 'color'];
  protected static $autoreshuffle = false;
  protected static function cast($card){
    if(!isset($card['location']))
      var_dump($card);

    $locations = explode('_', $card['location']);
    return [
      'id' => $card['id'],
//      'uid' => ($card['color'] - 1)*10+($card['value'] - 1), // Used by stock component
      'location' => $locations[0],
      'value' => $card['value'],
      'color' => $card['color'],
      'pId' => $locations[1] ?? null,
    ];
  }


  public static function getOfPlayer($pId){
    return self::getInLocation(['hand', $pId])->toArray();
  }

  public static function getOnTable($pId = '%'){
    return self::getInLocation(['table', $pId])->toArray();
  }


  //////////////////////////////////
  //////////////////////////////////
  ///////////// SETUP //////////////
  //////////////////////////////////
  //////////////////////////////////

  public function setupNewGame($players, $options)
  {
    $challenge = count($players) == 3 && $options[OPTION_CHALLENGE] == CHALLENGE_ON;

    $colors = [
      CARD_BLUE => 9,
      CARD_GREEN => $challenge? 0 : 9,
      CARD_PINK => 9,
      CARD_YELLOW => 9,
      CARD_ROCKET => 4
    ];


    foreach($colors as $cId => $maxValue) {
      for($value = 1; $value <= $maxValue; $value++) {
        $cards[] = [
          'color' => $cId,
          'value' => $value
        ];;
      }
    }

    self::create($cards, 'deck');
    self::shuffle('deck');
  }



  public static function clearMission()
  {
    // Take back all cards (from any location => null) to deck and shuffle
    self::moveAllInLocation(null, "deck");
    self::shuffle('deck');

/*
    // Deal communication cards
    $coms = $this->cards->getCardsOfType(COMM);
    foreach($coms as $card_id => $card) {
      $player_id = array_shift($players)['player_id'];
      $this->cards->moveCard( $card_id, 'comm', $player_id);
    }
*/
  }


  public static function startNewMission()
  {
    $players = Players::getAll();
    $nbCards = intdiv(Globals::isChallenge()? 30 : 40 , $players->count() );

    // This guy will have one extra card if 3 players and challenge mode off
    $luckyGuy = (count($players) == 3 && !Globals::isChallenge())? array_rand($players) : -1;

    foreach($players as $pId => $player){
      $hand = self::pickForLocation($nbCards + ($pId == $luckyGuy? 1 : 0), 'deck', ["hand", $pId] );
      Notifications::newHand($pId, $hand->toArray());
    }

    $card4Rocket = self::getSelectQuery()->where('value',4)->where('color', CARD_ROCKET)->get();
    return $card4Rocket['pId'];
  }


  public static function play($card)
  {
    self::move($card['id'], ['table', $card['pId'] ]);
  }
}
