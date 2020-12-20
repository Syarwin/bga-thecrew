<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\LogBook;
use CREW\Cards;

/*
 * Handle a trick
 */
trait TrickTrait
{
  /*
   * Initialize a new trick : reset trick color, increase trick count
   */
  function stNewTrick()
  {
    Globals::startNewTrick();
    $status = LogBook::getStatus();
    $newState = ($status['distress'] && Globals::getTrickCount() == 1)? 'distress' : 'next';
    $this->gamestate->nextState($newState);
  }



  /*
   * Anyone asked for communication ?
   */
  function stBeforeComm()
  {
    $this->gamestate->nextState('turn');
/*
      $sql = "SELECT player_id, comm_token FROM player where comm_pending = 1 and comm_token <> 'used' limit 1";
      $player_id = self::getUniqueValueFromDb( $sql );

      $mission = $this->getMission();
      $disruption = array_key_exists('disruption', $mission) && $mission['disruption'] > self::getGameStateValue( 'trick_count');

      if(!$disruption && $player_id != NULL)
      {
          self::notifyAllPlayers('note', clienttranslate('${player_name} starts communication'),array(
              'player_name' => self::getPlayerName($player_id)
          ));
          $this->gamestate->changeActivePlayer($player_id);
          $this->gamestate->nextState('comm');
      }
      else
      {
          $this->gamestate->changeActivePlayer(self::getGameStateValue('last_winner'));
          $this->gamestate->nextState('turn');
      }
  */
  }


  function argPlayerTurn()
  {
    $player = Players::getActive();
    $hand = $player->getCards();
    $color = Globals::getTrickColor();

    // If not the first card of the trick
    if($color != 0){
      // Keep only the cards with matching color (if at least one such card)
      $filteredHand = array_values(array_filter($hand, function($card) use ($color){ return $card['color'] == $color; }));
      if(!empty($filteredHand))
        $hand = $filteredHand;
    }

    // Can the current player activate distress ?
    $status = LogBook::getStatus();
    $cardPlayed = count(Cards::getOnTable());
    $noCommunicationBefore = !Players::alreadyCommmunicate();
    $canDistress =  !$status['distress'] && $cardPlayed == 0 && $noCommunicationBefore && Globals::getTrickCount() ==1;

    return [
      'cards' => array_map(function($card){ return $card['id'];}, $hand),
      'canDistress' => $canDistress
    ];
  }

  function actPlayCard($cardId) {
    self::checkAction("actPlayCard");

    $card = Cards::get($cardId);
    $player = Players::get($card['pId']);
    Cards::play($card);
    Notifications::playCard($card, $player);

    // Set the trick color if it hasn't been set yet
    if (Globals::getTrickColor() == 0) {
      Globals::setTrickColor($card['color']);
    }

    $this->gamestate->nextState('next');

    // TODO : if card is communicated card

/*
              if($reminder_card != null)
              {
                  // And notify
                  self::notifyAllPlayers('resetComm', '', array(
                      'card' => $reminder_card,
                      'player_id' => $player_id,
                      'reminder_id' => $reminder_card['id']
                  ));
              }
              */

  }


/*
case 'comm':
    //reminder card
    $reminder = $this->getCommunicationCard($player_id);

    $this->cards->moveCard($card_id, 'comm', $player_id);
    $this->cards->moveCard($reminder['id'], 'hand', $player_id);

    // And notify
    self::notifyAllPlayers('commCard', '', array(
        'card_id' => $card_id,
        'card' => $card,
        'player_id' => $player_id,
        'reminder_id' => $reminder['id']
    ));

    self::notifyPlayer($player_id, 'commpending', '', array(
        'pending' => 0,
        'canCommunicate' => 0
    ));

    $mission = $this->getMission();

    if(array_key_exists('deadzone', $mission))
    {
        //dead zone
        $sql = "update player set comm_token = 'used' where player_id=".$player_id;
        self::DbQuery( $sql );

        self::notifyAllPlayers('endComm', clienttranslate('${player_name} tells ${value_symbol}${color_symbol} is ${comm_place}'),array(
            'player_name' => self::getPlayerName(self::getActivePlayerId()),
            'comm_place' => '...',
            'comm_status' => 'used',
            'card_id' => $card['id'],
            'card' => $card,
            'player_id' => self::getActivePlayerId(),
            'value' => $card['type_arg'],
            'value_symbol' => $card['type_arg'], // The substitution will be done in JS format_string_recursive function
            'color' => $card['type'],
            'color_symbol' => $card['type'] // The substitution will be done in JS format_string_recursive function
        ));

        $this->gamestate->nextState('after');
        return;
    }
    else
    {
        $sql = "update player set comm_token = 'hidden' where player_id=".$player_id;
        self::DbQuery( $sql );
    }

break;

case "distress":
    $sql = "update player set card_id = ".$card_id." where player_id=".$player_id;
    self::DbQuery( $sql );

    $this->gamestate->setPlayerNonMultiactive($this->getCurrentPlayerId(), "next" );
    return;

    break;
}

// Next player
$this->gamestate->nextState('next');
*/


  function stNextPlayer() {
    // Each player played a card ? => end of trick
    $nPlayers = Players::count();
    if (count(Cards::getOnTable()) == $nPlayers) {
      $this->stEndOfTrick();
      return;
    }

    $pId = self::activeNextPlayer();
    self::giveExtraTime($pId);
    $this->gamestate->nextState('nextPlayer');
  }



  function stEndOfTrick(){
    // This is the end of the trick

    $last_trick = $this->cards->countCardInLocation('hand') == 0;

    $cards_on_table = $this->cards->getCardsInLocation('cardsontable');
    $best_value = 0;
    $best_value_player_id = null;
    $winningColor = self::getGameStateValue('trick_color'); // The color needed to win the trick color unless a trump (5) was played

    // Who wins ?
    foreach ($cards_on_table as $card) {
        // Note: type = card color
        // Note: type_arg = value of the card
        // Note: location_arg = player who played this card on table
        if ($card['type'] == 5 && $winningColor != 5) { // A trump has been played: this is the first one
            $winningColor = 5; // Now trumps are needed to win the trick
            $best_value_player_id = $card['location_arg'];
            $best_value = $card['type_arg'];
        }
        else if($card['type'] == $winningColor) { // This card is the right color to win the trick
            if($card['type_arg'] > $best_value) {
                $best_value_player_id = $card['location_arg'];
                $best_value = $card['type_arg'];
            }
        }
    }

    // Transfer all remaining cards to the winner of the trick
    self::DbQuery(sprintf("UPDATE player SET player_trick_number = player_trick_number+1 WHERE player_id='%s'", $best_value_player_id));
    $this->cards->moveAllCardsInLocation('cardsontable', 'trick'.self::getGameStateValue('trick_count'), null, $best_value_player_id);

    // Notify
    // Note: we use 2 notifications here in order we can pause the display during the first notification
    //  before we move all cards to the winner (during the second)
    self::notifyAllPlayers('trickWin', clienttranslate('${player_name} wins the trick:&nbsp;<br />${cards}'), array(
        'player_id' => $best_value_player_id,
        'player_name' => self::getPlayerName($best_value_player_id),
        'cards' => self::listCardsForNotification($cards_on_table),
    ));

    self::notifyAllPlayers('giveAllCardsToPlayer', '', array(
        'player_id' => $best_value_player_id,
        'cards' => $cards_on_table
    ));

    self::setGameStateValue('last_winner', $best_value_player_id);

    $className = 'THCCheck';
    $mission = self::getUniqueValueFromDB( "SELECT max(mission) FROM logbook");
    if(in_array($mission,$this->specificCheck))
    {
        $className = $className.$mission;
    }
    $check = new $className($this);
    $check->check();

    if(self::getGameStateValue( 'mission_finished')!=0)
    {
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->nextState("endMission");
    }
    else
    {
        if($this->getMission()['id'] == 12 && self::getGameStateValue( 'trick_count') == 1)
        {
            $this->swapOneCard();
        }
        $this->gamestate->changeActivePlayer($best_value_player_id);
        $this->gamestate->nextState("nextTrick");
    }
  }
}
