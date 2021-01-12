<?php
namespace CREW\Missions;
use \CREW\Tasks;
use \CREW\Game\Notifications;

class Mission23 extends AbstractMission
{
  public function __construct(){
    $this->id = 23;
    $this->desc = clienttranslate('Most of the modules are still on emergency back-up supply while you puzzle over the cause of the rapid cooling. Callisto, one of the 79 moons of Jupiter, passes by, the moment you appear to escape the frost field. <b>Before you select the task cards, you can swap the position of two task tokens. Decide together but do not reveal anything about your own cards.</b>');
    $this->tasks = 5;
    $this->tiles = [1,2,3,4,5];
  }

  public function getStartingState()
  {
    return 'moveTile';
  }


  public function argMoveTile($tasks)
  {
    $result = [];
    foreach($tasks as $task){
      $result[$task['id']] = [];
      foreach($tasks as $task2){
        if($task['id'] == $task2['id'])
          continue;

        $result[$task['id']][] = $task2['id'];
      }
    }

    return $result;
  }

  public function actMoveTile($id1, $id2)
  {
    $task1 = Tasks::get($id1);
    $task2 = Tasks::get($id2);

    Tasks::setTile($id1, $task2['tile']);
    Tasks::setTile($id2, $task1['tile']);
    Notifications::swapTiles(Tasks::get($id1), Tasks::get($id2));
  }
}
