<?php
namespace CREW\Missions;
use \CREW\Game\Players;
use \CREW\Game\Globals;
use CREW\Game\GlobalsVars;
use \CREW\Game\Notifications;
use \CREW\Cards;
use thecrew;

class Mission12 extends AbstractMission
{
  public function __construct(){
    $this->id = 12;
    $this->desc = clienttranslate('You watch tensely as you pass remarkably close to the meteorites. In all of the excitement, you have messed up your records, causing a few minutes of confusion. <b>Immediately after the first trick, each of you must draw a random card from the crew member to your right and add it to your hand. Then continue playing normally.</b>');
    $this->tasks = 4;
    $this->tiles = ['o'];
    $this->informations = [
      'cardsType' => 'swap',
    ];
  }

  public function check($lastTrick)
  {
    parent::check($lastTrick);
    if(Globals::getTrickCount() != 1)
      return;

    // Compute the cards that will turn around
    $cards = [];
    $jarvisGivingCard = null;

    foreach(Players::getAll() as $pId => $player){
      $cards[$pId] = $player->getRandomCard();
      if ($pId === JARVIS_ID) {
        $jarvisGivingCard = $cards[$pId]['id'];
      }
    }

    // First handle Jarvis card
    if (GlobalsVars::isJarvis()) {
      $targetId = Players::getNextId($player);
      $target = Players::get($targetId);
      $card = Cards::get($jarvisGivingCard);
      Cards::move($card['id'], ['hand', $targetId]);
      Notifications::swapCard(Players::getJarvis(), $target, $card, null);
    }

    // Notify
    foreach(Players::getAll() as $pId => $player){
      if ($pId == \JARVIS_ID) {
        continue;
      }
      $targetId = Players::getNextId($player);
      $target = Players::get($targetId);
      Cards::move($cards[$pId]['id'], ['hand', $targetId]);

      if ($targetId == \JARVIS_ID) {
        $this->swapCardJarvis($player, $target, $cards[$pId]['id'], $jarvisGivingCard);
      } else {
        Notifications::swapCard($player, $target, $cards[$pId], null);
      }
    }
  }

  public function swapCardJarvis($player, $target, $cardId, $jarvisGivingCard) {
    $card = Cards::get($cardId);
    $jarvisColumn = null;
    $cardList = GlobalsVars::getJarvisCardList();

    $found = false;
    foreach ($cardList as $column => &$cards) {
      if (!$found) {
        foreach ($cards as $i => $c) {
          if ($c['id'] == $jarvisGivingCard) {
            $cards[$i] = [
              'id' => (int) $card['id'],
              'hidden' => true,
            ];
            GlobalsVars::setJarvisCardList($cardList);
            $found = true;
            $jarvisColumn = $column;
            break;
          }
        }
      }
    }

    Notifications::swapCard($player, $target, $card, $jarvisColumn);
  }
}
