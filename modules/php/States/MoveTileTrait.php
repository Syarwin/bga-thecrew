<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\LogBook;
use CREW\Cards;
use CREW\Missions;
use CREW\Tasks;

/*
 * Move tile, specific to mission 23 and 40
 */
trait MoveTileTrait
{
  function argMoveTile(){
    $mission = Missions::getCurrent();
    $tasks = Tasks::getUnassigned();
    return [
      'tasks' => $tasks,
      'tiles' => $mission->argMoveTile($tasks),
    ];
  }

/*
      $result = array();
      $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NULL";
      $result['tasks'] = self::getCollectionFromDb( $sql );
      $result['ids'] = array();

      $mission = $this->getMission();

      switch($mission['id'])
      {

          case 40:
              $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where token = ''";
              $tasks = self::getCollectionFromDb( $sql );

              for($i=1;$i<=3;$i++)
              {
                  $result['ids']['marker_'.$i] = array();
                  foreach($tasks as $task_id => $task)
                  {
                      $result['ids']['marker_'.$i]['task_'.$task_id] = 'task_'.$task_id;
                  }
              }

          break;
      }

      return $result;
  }
}
*/

  function actMoveTile($id1, $id2)
  {
    $mission = Missions::getCurrent();
    $mission->actMoveTile($id1, $id2);
    $this->gamestate->nextState('task');
  }
    /*
      self::checkAction("actMultiSelect");

      $mission = $this->getMission();

      switch($mission['id'])
      {

          case 40:
              $idl1 = str_replace ('marker_','', $id1);
              $idl2 = str_replace ('task_','', $id2);

              $sql = "update task set token = '' where token='".$idl1."'";
              self::DbQuery( $sql );

              $sql = "update task set token = '".$idl1."' where task_id=".$idl2;
              self::DbQuery( $sql );

              $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where task_id=".$idl2;
              $task2 = self::getObjectFromDb( $sql );

              self::notifyAllPlayers('move', '' ,array(
                  'player_id' => self::getCurrentPlayerId(),
                  'player_name' => self::getPlayerName(self::getCurrentPlayerId()),
                  'task' => $task2,
                  'item_id' => $id1,
                  'location_id' => 'task_'.$idl2
              ));
              $this->gamestate->nextState('task');

              break;
      }
      */

  function actPassMoveTile()
  {
    $this->gamestate->nextState('task');
  }

}
