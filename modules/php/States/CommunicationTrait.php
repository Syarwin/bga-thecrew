<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\LogBook;
use CREW\Cards;
use CREW\Missions;

/*
 * Handle communication
 */
trait CommunicationTrait
{
  /*
   * Anyone asked for communication ?
   */
  function stBeforeComm()
  {
    $nextPlayer = Globals::getLastWinner();
    $newState = 'turn';

    $player = Players::getNextToCommunicate();
    $mission = Missions::getCurrent();
    if(!$mission->isDisrupted() && !is_null($player) && $player->canCommunicate()){
      $player->toggleComm();
      Notifications::startComm($player);
      $nextPlayer = $player;
      $newState = 'comm';
    }

    $this->gamestate->changeActivePlayer($nextPlayer->getId());
    $this->gamestate->nextState($newState);
  }

  /*
   * Toggle comm pending : if possible, start comm
   */
  function actToggleComm()
  {
    $player = Players::getCurrent();
    $player->toggleComm();
    Notifications::toggleCommPending($player);

    $mission = Missions::getCurrent();
    $stateName = $this->gamestate->state()['name'];
    if(!$mission->isDisrupted() && $player->isCommPending() && $stateName == 'playerTurn' && Cards::countOnTable() == 0 && $player->canCommunicate()){
      $this->gamestate->nextState('startComm');
    }
  }


  /*
   * Return the list of all possible cards to communicate with associated status
   */
  function argComm()
  {
    $player = Players::get();
    $cards = $player->getCards();
    $mission = Missions::getCurrent();

    // Compute min§max of each value
    $min = [null, 10, 10, 10, 10];
    $max = [null, 0, 0, 0, 0];
    foreach($cards as $card){
      if($card['color'] == CARD_ROCKET)
        continue;

      $c = $card['color'];
      $min[$c] = min($min[$c], $card['value']);
      $max[$c] = max($max[$c], $card['value']);
    }

    // Get corresponding cards
    $result = [];
    foreach($cards as $card){
      $c = $card['color'];
      $v = $card['value'];
      if($c == CARD_ROCKET || ($v != $min[$c] && $v != $max[$c]))
        continue;

      $status = ($max[$c] == $min[$c]? 'middle' : ($v == $min[$c]? 'bottom' : 'top'));
      if($mission->isDeadZone())
        $status = 'hidden';

      $result[] = [
        'card' => $card,
        'status' => $status
      ];
    }

    return [
      'cards' => $result,
      'pId' => $player->getId(),
    ];
  }


  /*
   * Cancel the pending communication and go to next player who want to communicate
   */
  function actCancelComm()
  {
    self::checkAction("actCancelComm");
    $player = Players::getCurrent();
    Notifications::cancelComm($player);
    $this->gamestate->nextState('cancel');
  }


  /*
   * Confirm the card and communicate to everyone
   */
  function actConfirmComm($cardId, $status)
  {
    self::checkAction("actConfirmComm");
    $player = Players::getCurrent();
    $player->communicate($cardId, $status);
    $card = $player->getCardOnComm();
    Notifications::communicate($player, $card, $status);
    $this->gamestate->nextState('next');
  }
}
