<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\Helpers\Utils;
use CREW\LogBook;
use CREW\Cards;

/*
 * Handle distress
 */
trait DistressTrait
{
  function stDistressSetup()
  {
    $this->gamestate->setAllPlayersMultiactive();
  }


  function actChooseDirection($dir)
  {
    $player = Players::getCurrent();
    $player->chooseDirection($dir);
    Notifications::chooseDirection($player, $dir);

    // Get other players choice that differs from mine
    $choices = Players::getAll()->assocMap(function($player){ return $player->getDistressChoice(); });
    $otherChoices = array_diff($choices, [$dir]);
    if(empty($otherChoices)){
      // Everyone agrees on same direction, let's go!
      Notifications::chooseDistressDirection($dir);
      Globals::setDistressDirection($dir);
      if($dir != DONT_USE){
        LogBook::launchDistressSignal();
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->nextState('next');
      } else {
        $this->gamestate->nextState('turn');
      }
    } else {
      $this->gamestate->setPlayersMultiactive(array_keys($otherChoices), '', true);
    }
  }

  function argDistress()
  {
    $data = [
      'dir' => Globals::getDistressDirection(),
      '_private' => []
    ];
    foreach(Players::getAll() as $player){
      $hand = $player->getCards();
      Utils::filter($hand, function($card){ return $card['color'] != CARD_ROCKET; });
      $data['_private'][$player->getId()] = array_map(function($card){ return $card['id'];}, $hand);
    }

    return $data;
  }


  function actChooseCardDistress($cardId)
  {
    self::checkAction("actChooseCardDistress", false);
    $card = Cards::get($cardId);
    $player = Players::getCurrent();
    $player->setDistressCard($cardId);
    Notifications::chooseDistressCard($player, $card);
    $this->gamestate->setPlayerNonMultiactive($player->getId(), "next");
  }


  function stDistressExchange()
  {
    foreach(Players::getAll() as $pId => $player){
      $targetId = Globals::getDistressDirection() == CLOCKWISE? $this->getPlayerAfter($pId) : $this->getPlayerBefore($pId);
      $target = Players::get($targetId);
      $card = $player->getDistressCard();
      Cards::move($card['id'], ['hand', $targetId]);
      Notifications::distressExchange($player, $target, $card);
    }
    Players::clearDistressCards();

    $this->gamestate->changeActivePlayer(Globals::getCommander());
    $this->gamestate->nextState('next');
  }
}
