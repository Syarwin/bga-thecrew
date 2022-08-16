<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\GlobalsVars;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\Missions;
use CREW\Tasks;

/*
 * Handle picking task
 */
trait PickTaskTrait
{
  /*
   * PickTask state : return the list of all unassigned tasks
   */
  function argPickTask()
  {
    return [
      'tasks' => Tasks::getUnassigned(),
      'jarvisActive' => GlobalsVars::isJarvisActive(),
    ];
  }


  /*
   * chooseTask action : assign the task to a player
   */
  function actChooseTask($taskId, $checkAction = true)
  {
    if ($checkAction) {
      self::checkAction('actChooseTask');
    }

    // Sanity check
    $taskIds = Tasks::getUnassignedIds();
    if(!in_array($taskId, $taskIds))
      throw new feException("You cannot pick this task");

    // Assign task and notify
    $player = Players::getActive();
    $task = Tasks::assign($taskId, $player);
    Notifications::assignTask($task, $player);

    $this->gamestate->nextState('next');
  }


  /*
   * Auto select last task to be picked by active player
   */
  function stPickTask()
  {
    $tasks = Tasks::getUnassigned();

    // automatically select last task
    // skip missions that commander need to select task for crew member
    if(count($tasks) == 1 && !in_array(Missions::getCurrentId(), [24,32,36,43])) {
      self::notifyAllPlayers( "wait2seconds", clienttranslate( 'Selecting the last task automatically' ), array() );
      $task = $tasks[0];
      $this->actChooseTask($task["id"], false);
    }
  }


  /*
   * Move on to next player to pick task, or start playing
   */
  function stNextPickTask()
  {
    if(count(Tasks::getUnassigned()) == 0){
      Players::changeActive(Globals::getCommander());
      $this->gamestate->nextState('turn');
    } else {
      Players::activeNext();
      $this->gamestate->nextState('task');
    }
  }
}
