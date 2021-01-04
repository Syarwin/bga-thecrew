<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\LogBook;
use CREW\Cards;
use CREW\Tasks;
use CREW\Missions;

/*
 * 5 players rule
 */
trait GiveTaskTrait
{
  public function stGiveTask()
  {
    $this->gamestate->setAllPlayersMultiactive();
  }

  public function argGiveTask()
  {
    return [
      'players' => Players::getAll()->assocMap(function($player){ return $player->getTasks(); })
    ];
  }

  public function actGiveTask($taskId, $pId)
  {
    self::checkAction('actGiveTask', false);

    $source = Players::get(self::getCurrentPlayerId());
    $target = Players::get($pId);
    $task = Tasks::get($taskId);
    Globals::storeTaskToGive($source->getId(), $taskId, $pId);

    Notifications::proposeGiveTask($source, $task, $target);
    $this->gamestate->nextState('askConfirmation');
  }

  function actPassGiveTask()
  {
    Notifications::passGiveTask();
    $this->gamestate->setPlayerNonMultiactive(self::getCurrentPlayerId(), "pass");
  }


  /*
   * Confirmation
   */
  public function stGiveTaskConfirmation()
  {
    $this->gamestate->setAllPlayersMultiactive();

    $transaction = Globals::getTaskToGive();
    $this->gamestate->setPlayerNonMultiactive($transaction['sourceId'], '');
  }

  public function argGiveTaskConfirmation()
  {
    $transaction = Globals::getTaskToGive();
    return [
      'player_name' => Players::get($transaction['sourceId'])->getName(),
      'sourceId' => $transaction['sourceId'],
      'targetId' => $transaction['targetId'],
      'task' => Tasks::get($transaction['taskId']),
    ];
  }


  function actConfirmGiveTask()
  {
    Notifications::confirmGiveTask();
    $this->gamestate->setPlayerNonMultiactive(self::getCurrentPlayerId(), "next");
  }

  function actRejectGiveTask()
  {
    Notifications::rejectGiveTask();
    $this->gamestate->nextState("reject");
  }


  function stGiveTaskExchange()
  {
    $transaction = Globals::getTaskToGive();
    $source = Players::get($transaction['sourceId']);
    $target = Players::get($transaction['targetId']);
    $task = Tasks::assign($transaction['taskId'], $target);
    Notifications::giveTask($source, $task, $target);
    $this->gamestate->nextState('next');
  }
}
