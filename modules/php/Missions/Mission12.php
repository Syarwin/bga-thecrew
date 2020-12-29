<?php
namespace CREW\Missions;
use \CREW\Game\Players;
use \CREW\Game\Globals;
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
    if(Globals::getTrickCount() != 1)
      return;

    // Compute the cards that will turn around
    $cards = [];
    foreach(Players::getAll() as $pId => $player){
      $cards[$pId] = $player->getRandomCard();
    }

    // Notify
    foreach(Players::getAll() as $pId => $player){
      $targetId = Players::getNextId($player);
      $target = Players::get($targetId);
      Cards::move($cards[$pId]['id'], ['hand', $targetId]);
      Notifications::swapCard($player, $target, $cards[$pId]);
    }
  }
}
