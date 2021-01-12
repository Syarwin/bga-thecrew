<?php
namespace CREW\Missions;
use \CREW\Tasks;
use \CREW\Game\Notifications;

class Mission40 extends AbstractMission
{
  public function __construct(){
    $this->id = 40;
    $this->desc = clienttranslate('You are now paying very close attention to the object, but are still uncertain what it might be. What appears in front of you could be a moon of Pluto. No wait, that’s no moon! You have found it! Planet Nine! Euphoria spreads among you and all the hardships are quickly forgotten. A surface scan of the planet suggests a solid crust. That would mean this is not another gas giant, it’s traversable. A fantastic opportunity is opening up. <b>Before you begin to distribute the task cards, you may move a task token to another task card that currently has no task tokens. Decide together but do not reveal anything about your own cards. </b>');
    $this->tasks = 8;
    $this->tiles = [1,2,3];
  }

  public function getStartingState()
  {
    return 'moveTile';
  }


  public function argMoveTile($tasks)
  {
    $tasksWithTile = [];
    $tasksWithoutTile = [];
    foreach($tasks as $task){
      if($task['tile'] != '')
        $tasksWithTile[] = $task['id'];
      else
        $tasksWithoutTile[] = $task['id'];
    }

    $result = [];
    foreach($tasksWithTile as $tId){
      $result[$tId] = $tasksWithoutTile;
    }

    return $result;
  }

  public function actMoveTile($id1, $id2)
  {
    $task1 = Tasks::get($id1);
    $task2 = Tasks::get($id2);

    Tasks::setTile($id1, '');
    Tasks::setTile($id2, $task1['tile']);
    Notifications::swapTiles(Tasks::get($id1), Tasks::get($id2));
  }
}
