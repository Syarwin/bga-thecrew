<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\GlobalsVars;
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
      if ($pId == \JARVIS_ID) {
        continue;
      }
      // Filter out rockets
      $hand = $player->getCards();
      Utils::filter($hand, function($card){ return $card['color'] != CARD_ROCKET; });

      // Find the name of target
      $targetId = Globals::getDistressDirection() == CLOCKWISE? Players::getNextId($pId, true) : Players::getPrevId($pId, true);
      $target = Players::get($targetId);

      $data['_private'][$player->getId()] = [
        'pId' => $target->getId(),
        'cards' => array_map(function($card){ return $card['id'];}, $hand)
      ];
    }

    if (GlobalsVars::isJarvis()) {
      $player = Players::getJarvis();
      $filteredHand = $player->getCards()->filter(function ($card) {
        return $card['id'] < 99 && $card['color'] != \CARD_ROCKET;
      });

      $data['_private'][Globals::getCommander()]['jarvis'] = $filteredHand->getIds();

      $targetId =
        Globals::getDistressDirection() == CLOCKWISE
          ? Players::getNextId(JARVIS_ID, true)
          : Players::getPrevId(JARVIS_ID, true);
      $data['_private'][Globals::getCommander()]['jarvisTarget'] = $targetId;
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

  /**
   * actChooseCardDistressJarvis: the commander choosed a card for him and Jarvis
   */
  function actChooseCardDistressJarvis($cardId, $jarvisCardId)
  {
    $this->gamestate->checkPossibleAction('actChooseCardDistress');

    $card = Cards::get($cardId);
    $player = Players::getCurrent();
    $player->setDistressCard($cardId);
    Notifications::chooseDistressCard($player, $card);

    $card = Cards::get($jarvisCardId);
    if ($card['pId'] != JARVIS_ID) {
      throw new feException('Card not owned by Jarvis. Should not happen');
    }
    GlobalsVars::setJarvisDistressCard($jarvisCardId);
    // Notifications::chooseDistressCardJarvis($player, $card);

    // Make the player inactive and change game state if no one if left active
    $this->gamestate->setPlayerNonMultiactive($player->getId(), 'next');
  }


  function stDistressExchange()
  {
    // First handle Jarvis card
    $jarvisGivingCard = null;
    if (GlobalsVars::isJarvis()) {
      $targetId =
        Globals::getDistressDirection() == CLOCKWISE
          ? Players::getNextId(JARVIS_ID, true)
          : Players::getPrevId(JARVIS_ID, true);
      $target = Players::get($targetId);
      $jarvisGivingCard = GlobalsVars::getJarvisDistressCard();
      $card = Cards::get($jarvisGivingCard);
      GlobalsVars::setJarvisDistressCard(null);
      Cards::move($card['id'], ['hand', $targetId]);
      Notifications::distressExchange(Players::getJarvis(), $target, $card, null);
    }

    foreach(Players::getAll() as $pId => $player){
      if ($pId == \JARVIS_ID) {
        continue;
      }
      $targetId = Globals::getDistressDirection() == CLOCKWISE? Players::getNextId($pId, true) : Players::getPrevId($pId, true);
      $target = Players::get($targetId);
      $card = $player->getDistressCard();
      Cards::move($card['id'], ['hand', $targetId]);

      $jarvisColumn = null;
      if ($targetId == \JARVIS_ID) {
        $cardList = GlobalsVars::getJarvisCardList();
        $found = false;
        foreach ($cardList as $column => &$cards) {
          if (!$found) {
            foreach ($cards as $i => $c) {
              if ($c['id'] == $jarvisGivingCard) {
                $cards[$i] = [
                  'id' => $card['id'],
                  'hidden' => false,
                ];
                GlobalsVars::setJarvisCardList($cardList);
                $found = true;
                $jarvisColumn = $column;
                break;
              }
            }
          }
        }
      }

      Notifications::distressExchange($player, $target, $card, $jarvisColumn);
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
