<?php
namespace CREW\Missions;
use \CREW\Game\Players;
use \CREW\Game\Notifications;
use \CREW\Tasks;

class Mission20 extends AbstractMission
{
  public function __construct(){
    $this->id = 20;
    $this->desc = clienttranslate('Finally, the dust cloud clears and the wild swings of the communication module have noticeably reduced. Before you appears Jupiter in all its splendor. The gas giant is clearly appropriately named. Your absolute awe is interrupted when you notice the two damaged radar sensors. <b>Your commander determines who receives the tasks and carries out the repair, the commander cannot choose himself or herself.</b>');
    $this->tasks = 2;
    $this->hiddenTasks = true;

    $this->question = clienttranslate('Are you OK to take the tasks?');
    $this->replies = [clienttranslate('Yes'), clienttranslate('No') ];
  }

  public function pickCrew($crewId)
  {
    $player = Players::get($crewId);
    foreach(Tasks::getUnassignedIds() as $taskId){
      // Assign task and notify
      $task = Tasks::assign($taskId, $player);
      Notifications::assignTask($task, $player);
    }
  }
}
