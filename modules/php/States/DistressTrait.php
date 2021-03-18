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
  function stPreDistress()
  {
    foreach(Players::getAll() as $player){
      $player->chooseDirection($player->getDistressAuto());
    }

    $choices = Players::getAllDistressChoices();
    $choices = array_values(array_diff($choices, [WHATEVER]));
    $needVote = (count($choices) > 1 || $choices[0] == 0);
    $this->gamestate->nextState($needVote? 'setup' : 'turn');
  }



  function stDistressSetup()
  {
    $this->gamestate->setAllPlayersMultiactive();
  }

  function argDistressSetup()
  {
    return [
      'players' => Players::getAllDistressChoicesAssoc()
    ];
  }

  function actChooseDirection($dir)
  {
    $player = Players::getCurrent();
    $player->chooseDirection($dir);
    Notifications::chooseDirection($player, $dir);

    // Get other players choice that differs from mine
    $choices = Players::getAllDistressChoices();
    $choices = array_values(array_diff($choices, [WHATEVER]));
    if(empty($choices) || (count($choices) == 1 && $choices[0] != 0)){
      $dir = empty($choices)? DONT_USE : $choices[0];

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
      if($dir == WHATEVER){
        $this->gamestate->setPlayerNonMultiactive($player->getId(), '');
      } else {
        $choices = Players::getAllDistressChoicesAssoc();
        $otherChoices = array_diff($choices, [$dir, WHATEVER]);
        $this->gamestate->setPlayersMultiactive(array_keys($otherChoices), '', true);
      }
    }
  }

  function argDistress()
  {
    $data = [
      'dir' => Globals::getDistressDirection(),
      '_private' => []
    ];
    foreach(Players::getAll() as $pId => $player){
      // Filter out rockets
      $hand = $player->getCards();
      Utils::filter($hand, function($card){ return $card['color'] != CARD_ROCKET; });

      // Find the name of target
      $targetId = Globals::getDistressDirection() == CLOCKWISE? $this->getPlayerAfter($pId) : $this->getPlayerBefore($pId);
      $target = Players::get($targetId);

      $data['_private'][$player->getId()] = [
        'pId' => $target->getId(),
        'cards' => array_map(function($card){ return $card['id'];}, $hand)
      ];
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


  /***********************
  ******* AUTOPICK *******
  ***********************/
  public function actSetAutopick($mode)
  {
    $player = Players::getCurrent();
    $player->setAutoPick($mode);
  }
}
