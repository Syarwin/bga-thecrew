<?php
namespace CREW;
use CREW\Game\Globals;
use CREW\Game\GlobalsVars;
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

  public static function getOrderedOnTable($pId = '%'){
    return self::getInLocationOrdered(['table', $pId]);
  }

  public static function countOnTable($pId = '%'){
    return self::getInLocation(['table', $pId])->count();
  }


  public static function getIds($cards){
    return array_map(function($card){ return $card['id']; }, $cards);
  }

  public static function getLastTrick(){
    return self::getInLocation(['trick'.  Globals::getTrickCount(), '%']);
  }

  public static function countInHand(){
    return self::getInLocation(['hand', '%'])->count();
  }

  //////////////////////////////////
  //////////////////////////////////
  ///////////// SETUP //////////////
  //////////////////////////////////
  //////////////////////////////////

  public static function setupNewGame($players, $options)
  {
    $challenge = count($players) <= 3 && $options[OPTION_CHALLENGE] == CHALLENGE_ON;

    $colors = [
      CARD_BLUE => 9,
      CARD_GREEN => $challenge? 0 : 9,
      CARD_PINK => 9,
      CARD_YELLOW => 9,
      CARD_ROCKET => 4
    ];


    foreach($colors as $cId => $maxValue) {
      $start = ($challenge && $cId == CARD_ROCKET)? 2 : 1;
      for($value = $start; $value <= $maxValue; $value++) {
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
  }


  public static function startNewMission()
  {
    $players = Players::getAll();
    $nbCards = intdiv(Globals::isChallenge() ? 30 : 40 , $players->count() );
    // This guy will have one extra card if 3 players and challenge mode off
    $luckyGuy = (count($players) <= 3 && !Globals::isChallenge())? array_rand($players->toAssoc()) : -1;

    if (GlobalsVars::isJarvis()) {
      $luckyGuy = !Globals::isChallenge() ? JARVIS_ID : -1; // If JARVIS is present, he will be the lucky guy
      self::startNewMissionJarvis($nbCards, $luckyGuy, $players);
    } else {
      foreach($players as $pId => $player){
        $hand = self::pickForLocation($nbCards + ($pId == $luckyGuy? 1 : 0), 'deck', ["hand", $pId] );
        Notifications::newHand($pId, $hand->toArray());
      }
    }

    $card4Rocket = self::getSelectQuery()->where('value',4)->where('color', CARD_ROCKET)->get();

    return $card4Rocket['pId'];
  }

  public static function startNewMissionJarvis($nbCards, $luckyGuy, $players)
  {
    // draw 10 or 14 cards for jarvis, without rocket 4
    $card4Rocket = self::getSelectQuery()->where('value', 4)->where('color', CARD_ROCKET)->get();
    self::DB()->update(['card_location' => 'pause'], $card4Rocket['id']);
    $cards = self::pickForLocation($nbCards + (JARVIS_ID == $luckyGuy ? 1 : 0), 'deck', ["hand", JARVIS_ID] )->toAssoc();

    // setup hidden and shown cards
    $hand = [];
    $col = 1;
    $hidden = true;

    $totalOfCards = count($cards);
    $lastCard = end($cards);
    $lastCardId = $lastCard["id"];
    $shouldShowLastCard = $totalOfCards % 2 > 0;

    foreach ($cards as $cId => $card) {
      // force to show last card, if number of cards is odd
      $hiddenCard = $lastCardId == $cId && $shouldShowLastCard ? false : $hidden;

      $hand[$col][] = ['id' => $cId, 'hidden' => $hiddenCard];
      $col += $hidden ? 0 : 1;
      $hidden = !$hidden;
    }

    GlobalsVars::setJarvisCardList($hand);
    Notifications::newJarvisHand(JarvisPlayer::getCards()->toArray());

    self::DB()->update(['card_location' => 'deck'], $card4Rocket['id']);

    foreach ($players as $pId => $player) {
      if ($pId == JARVIS_ID) {
        continue;
      }

      $hand = self::pickForLocation($nbCards + ($pId == $luckyGuy? 1 : 0), 'deck', ["hand", $pId] );
      Notifications::newHand($pId, $hand->toArray());
    }
  }

  public static function play($card)
  {
    self::move($card['id'], ['table', $card['pId'] ]);
  }


  public static function winTrick($cards, $player)
  {
    self::move(self::getIds($cards), ['trick'. Globals::getTrickCount(), $player->getId() ]);
  }

  public static function getRemeaningRockets()
  {
    return self::getInLocationQ(['hand', '%'])->where('color', CARD_ROCKET)->get(false);
  }

  public static function getRemeaningOfValue($value)
  {
    return self::getInLocationQ(['hand', '%'])->where('value', $value)->where('color', '<>', CARD_ROCKET)->get(false);
  }

  public static function getRemeaningOfColor($color)
  {
    return self::getInLocationQ(['hand', '%'])->where('color', $color)->get(false);
  }

  public static function countRemeaning()
  {
    return self::getInLocation(['hand', '%'])->count();
  }

}
