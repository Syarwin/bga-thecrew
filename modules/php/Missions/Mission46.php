<?php
namespace CREW\Missions;
use \CREW\Cards;
use \CREW\Game\Globals;
use \CREW\Game\Players;
use \CREW\Game\Notifications;

class Mission46 extends AbstractMission
{
  public function __construct(){
    $this->id = 46;
    $this->desc = clienttranslate('While you are being assailed with an overwhelming amount of information, you find you are still able to instinctively react to danger. Suddenly the main modules of the ship shut down during the jump, and red warning lights and alarms tear you from of your previously trance-like state. <b>Your task is that the crew member to the left of the member with the pink nine must win all pink cards. Say who has pink nine.</b>');

    $this->informations = [
      'special' => clienttranslate('All Pink Cards'),
      'specialTooltip' => clienttranslate('This crew member must win all the pink cards'),
    ];
  }

  public function prepare()
  {
    $card = Cards::getSelectQuery()->where('color', CARD_PINK)->where('value', 9)->get(true);
    $player = Players::get($card['pId']);

    // Notify the player
    Notifications::message(clienttranslate('${player_name} has ${value_symbol}${color_symbol}'), [
      'player' => $player,
      'card' => $card,
    ]);

    // Make the next player special
    $special = Players::getNextId($player);
    Globals::setSpecial($special);
    Notifications::specialMemberMission46(Players::get($special));
  }


  public function check($lastTrick)
  {
    $containPink = array_reduce($lastTrick['cards'], function($carry, $card){ return $carry || $card['color'] == CARD_PINK; }, false);
    $remeainingPink = Cards::getRemeaningOfColor(CARD_PINK);

    if($containPink && !$lastTrick['winner']->isSpecial()){
      $this->fail();
    }
    else if(Globals::isLastTrick() || $remeainingPink->empty()){
      $this->success();
    }
    else {
      $this->continue();
    }
  }
}
