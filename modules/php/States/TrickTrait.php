<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\GlobalsVars;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\LogBook;
use CREW\Cards;
use CREW\Tasks;
use CREW\Missions;

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
    $newState = 'next';
    // If first trick, go to distress or 5 players special rule
    if(Globals::getTrickCount() == 1){
      $mission = Missions::getCurrent();
      $nPlayers = Players::count();
      $newState = ($nPlayers == 5 && $mission->isSpecialWithFivePlayers())? 'giveTask' : 'distress';
    }
    Notifications::newTrick();
    $this->gamestate->nextState($newState);
  }



  /*
   * Return the list of cards that can be played during the trick
   *  also compute whether the destress signal can be activated or not
   */
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

    $cards = array_map(function($card){ return $card['id'];}, $hand);
    $commCard = $player->getCardOnComm();
    return [
      'cards' => $cards,
      'canDistress' => LogBook::canActivateDistress(),
      'commCard' => $commCard,
      'canPlayCommunicatedCard' => ($commCard != null && in_array($commCard['id'], $cards)),
      'jarvisActive' => GlobalsVars::isJarvisActive(),
    ];
  }



  function actPlayCard($cardId) {
    $this->gamestate->checkPossibleAction("actPlayCard");

    $cards = $this->argPlayerTurn()['cards'];
    if(!in_array($cardId, $cards))
      throw new \BgaUserException(_("You cannot play this card"));

    $card = Cards::get($cardId);
    $player = Players::get($card['pId']);
    Cards::play($card);
    Notifications::playCard($card, $player);

    // Set the trick color if it hasn't been set yet
    if (Globals::getTrickColor() == 0) {
      Globals::setTrickColor($card['color']);
    }

    // If card played is communicated card
    $commCard = $player->getCardOnComm();
    if(!is_null($commCard) && $commCard['id'] == $cardId){
      $player->usedComm();
      Notifications::usedComm($player);
    }

    $this->gamestate->nextState('next');
  }


  function actPreselectCard($cardId) {
    $this->gamestate->checkPossibleAction("actPreselectCard");

    $card = Cards::get($cardId);
    $player = Players::get($card['pId']);
    $currentPlayer = Players::getCurrent();
    if($player->getId() != $currentPlayer->getId())
      throw new \BgaUserException(_("This is not one of your card"));

    $preselectedCard = $player->getPreselectedCard();
    if($preselectedCard['id'] == $cardId){
      // Unselect
      $player->clearPreselect();
      Notifications::clearPreselect($player);
    }
    else {
      $player->preselectCard($cardId);
      Notifications::preselect($player, $card);
    }
  }


  function stPlayerTurn(){
    $player = Players::getActive();
    $card = $player->getPreselectedCard();

    // Player preselect something ?
    if($card != null){
      $cards = $this->argPlayerTurn()['cards'];
      if(!in_array($card['id'], $cards)){
        $player->clearPreselect();
        Notifications::clearPreselect($player, true);
      } else {
        self::actPlayCard($card['id']);
      }
    }
  }


  /*
   * Either go to next player or end the trick depeding on the number of cards played
   */
  function stNextPlayer() {
    // Each player played a card ? => end of trick
    $nPlayers = Players::count();
    if (count(Cards::getOnTable()) == $nPlayers) {
      $this->stEndOfTrick();
      return;
    }

    $pId = Players::activeNext();
    self::giveExtraTime($pId);
    $this->gamestate->nextState('nextPlayer');
  }



  /*
   * End of trick
   */
  function stEndOfTrick(){
    $cards = Cards::getOnTable();
    $jarvisCard = null;
    $winningColor = Globals::getTrickColor();
    $bestCard = null;

    // Who wins ?
    foreach ($cards as $card) {
      if ($card['color'] == CARD_ROCKET && $winningColor != CARD_ROCKET) { // A trump has been played: this is the first one
          $winningColor = CARD_ROCKET; // Now trumps are needed to win the trick
          $bestCard = $card;
      }
      else if($card['color'] == $winningColor) { // This card is the right color to win the trick
        if(is_null($bestCard) || $card['value'] > $bestCard['value']) {
          $bestCard = $card;
        }
      }

      if ($card['pId'] == JARVIS_ID) {
        $jarvisCard = $card;
      }
    }
    $winner = Players::get($bestCard['pId']);

    // Transfer the cards, inc trick counter and notify
    Cards::winTrick($cards, $winner);
    $winner->winTrick();
    Notifications::winTrick($cards, $winner);
    Globals::setLastWinner($winner);

    // Update tasks
    Tasks::checkLastTrick();
    $mission = Missions::getCurrent();
    $mission->check([
      'bestCard' => $bestCard,
      'winner' => $winner,
      'cards' => $cards,
    ]);

    if ($jarvisCard != null) {
      $player = Players::get(JARVIS_ID);
      $column = $player->getCardColumn($jarvisCard);
      $cards = $player->getCards(false, $column)->toArray();

      // only 1 cards => remove it
      if (count($cards) == 1) {
        $cards = [];
      } else {
        // We played the second card => remove it and "unhide" the first one
        unset($cards[1]);
        $cards[0]['hidden'] = false;
        Notifications::jarvisRevealNewCard(Cards::get($cards[0]['id']), $column);
      }

      // Update card list
      $cardList = GlobalsVars::getJarvisCardList();
      $cardList[$column] = $cards;
      GlobalsVars::setJarvisCardList($cardList);
    }

    $status = $mission->getStatus();
    if($status != 0){
      $msg = $status > 0? clienttranslate('Mission ${nb} completed') : clienttranslate('Mission ${nb} failed');
      Notifications::message($msg, ['nb' =>  $mission->getId() ]);
      Globals::setMissionFinished($status);
      GlobalsVars::setJarvisActive(false);
//      $this->gamestate->setAllPlayersMultiactive();
      $this->gamestate->nextState("endMission");
    } else {
      Players::changeActive($winner->getId());
      $this->gamestate->nextState("nextTrick");
    }
  }
}
