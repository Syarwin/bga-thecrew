<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\Helpers\Utils;
use CREW\LogBook;
use CREW\Cards;
use CREW\Missions;
use CREW\Tasks;

/*
 * Handle question/answer
 */
trait QuestionTrait
{
  function argQuestion()
  {
    $mission = Missions::getCurrent();

    return [
      'i18n' => ['question'],
      'player_name' => Players::getCommander()->getName(),
      'question' => $mission->getQuestion(),
      'replies' => $mission->getReplies(),
      'tasks' => Tasks::getUnassigned(),
    ];

    /*
    TODO : 'down' stuff
      $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NULL";
      $result['tasks'] = self::getCollectionFromDb( $sql );

      $down = array_key_exists('down', $mission);
      if($down)
      {
          foreach($result['tasks'] as $task_id => $task) {
              $result['tasks'][$task_id]['card_type'] = 7;
              $result['tasks'][$task_id]['card_type_arg'] = 0;
          }
      }
      */
  }

  function actReply($i)
  {
    $replies = Missions::getCurrent()->getReplies();
    $reply = $replies[$i];
    $player = Players::getCurrent();
    Notifications::speak($player, $reply);
    $this->gamestate->nextState('next');
  }


  function stNextQuestion()
  {
    $this->activeNextPlayer();
    $newState = $this->getActivePlayerId() == Globals::getCommander()? 'pick' : 'next';
    $this->gamestate->nextState($newState);
  }


  function argPickCrew()
  {
    $playerIds = Players::getAll()->getIds();
    Utils::diff($playerIds, [Globals::getCommander()]);
    return [
      'players' => $playerIds,
      'tasks' => Tasks::getUnassigned(),
    ];
      /*
      $result = array();
      $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NULL";
      $result['tasks'] = self::getCollectionFromDb( $sql );
      $mission = $this->getMission();
      $down = array_key_exists('down', $mission);
      if($down)
      {
          foreach($result['tasks'] as $task_id => $task) {
              $result['tasks'][$task_id]['card_type'] = 7;
              $result['tasks'][$task_id]['card_type_arg'] = 0;
          }
      }

      $evenly = 10;
      $evenlyLeft = 0;

      if(array_key_exists('distribution', $mission))
      {
          $nbPlayers = thecrew::getUniqueValueFromDB("SELECT count(*) from player");
          $evenly = intdiv($mission['tasks'], $nbPlayers);
          $evenlyLeft  = $mission['tasks'] % $nbPlayers;
          $playersAtMax  = thecrew::getUniqueValueFromDB("SELECT COUNT(*) FROM (SELECT count(player_id) from task where player_id is not null group by player_id having count(player_id)>".$evenly.") t");
          if($playersAtMax == NULL) $playersAtMax = 0;
      }
      $taskLeft =  $mission['tasks'] - thecrew::getUniqueValueFromDB("SELECT count(*) from task where player_id is not null");

      $result['possible'] = array();
      $sql = "SELECT player_id id, comm_token FROM player ";
      $result['players'] = self::getCollectionFromDb( $sql );
      foreach($result['players'] as $player_id => $player) {

          $nbMissions = self::getUniqueValueFromDB( "SELECT count(*) FROM task where player_id=".$player_id);
          if($nbMissions < $evenly || ($nbMissions == $evenly && $evenlyLeft> $playersAtMax) )
          {
              if(($mission['id'] != 33 && $mission['id'] != 41 && $mission['id'] != 20) || $player_id != self::getGameStateValue('commander_id'))
              {
                  $result['possible'][$player_id] = $player_id;
              }
          }
      }
      return $result;
      */
  }


  function actPickCrew($crewId)
  {
    self::checkAction("actPickCrew");
    $player = Players::getCurrent();
    $mission = Missions::getCurrent();
    $mission->pickCrew($crewId);

    $newState = count(Tasks::getUnassigned()) > 0? 'task' : 'trick';
    $this->gamestate->nextState($newState);
  }
/*
  $distribution = array_key_exists('distribution', $mission);
  $down = array_key_exists('down', $mission);

  if(!$distribution && !$down)
  {
    if(self::getGameStateValue( 'special_id') == 0)
    {
    self::setGameStateValue( 'special_id', $crew_id );
    self::notifyAllPlayers('special', clienttranslate('${player_name} chooses ${special_name}'), array(
    'player_id' => $crew_id,
    'player_name' => $activePlayer['player_name'],
    'special_name' => $this->getPlayerName($crew_id)
    ));
    }
    else
    {
    self::setGameStateValue( 'special_id2', $crew_id );
    self::notifyAllPlayers('special', clienttranslate('${player_name} chooses ${special_name} as second special crew'), array(
    'player_id' => $crew_id,
    'player_name' => $activePlayer['player_name'],
    'special_name' => $this->getPlayerName($crew_id),
    'special2' => true
    ));
    }
  }

  if($mission['id'] == 50)
  {
  if(self::getGameStateValue( 'special_id2') == 0)
  {
  $this->gamestate->nextState( 'pickCrew' );
  return;
  }
  }
  else if($down)
  {
  $sql = "update task set player_id =".$crew_id;
  self::DbQuery( $sql );

  $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NOT NULL";
  $tasks = self::getCollectionFromDb( $sql );

  foreach($tasks as $task_id => $task) {

  self::notifyAllPlayers('takeTask', clienttranslate('${player_name} takes task ${value_symbol}${color_symbol}'), array(
  'task' => $task,
  'player_id' => $crew_id,
  'player_name' => $this->getPlayerName($crew_id),
  'value' => $task['card_type_arg'],
  'value_symbol' => $task['card_type_arg'], // The substitution will be done in JS format_string_recursive function
  'color' => $task['card_type'],
  'color_symbol' => $task['card_type'] // The substitution will be done in JS format_string_recursive function
  ));
  }
  $mission['tasks'] = 0;
  }
  else if($distribution)
  {
  $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NULL";
  $task = self::getObjectFromDb( $sql );

  $sql = "update task set player_id=".$crew_id." where task_id = ".$task['task_id'];
  self::DbQuery( $sql );

  $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where task_id = ".$task['task_id'];
  $task = self::getObjectFromDB( $sql );
  self::setGameStateValue( 'special_id', 0 );
  self::setGameStateValue( 'special_id2', 0 );

  self::notifyAllPlayers('takeTask', clienttranslate('${player_name} takes task ${value_symbol}${color_symbol}'), array(
  'task' => $task,
  'player_id' => $crew_id,
  'player_name' => $this->getPlayerName($crew_id),
  'value' => $task['card_type_arg'],
  'value_symbol' => $task['card_type_arg'], // The substitution will be done in JS format_string_recursive function
  'color' => $task['card_type'],
  'color_symbol' => $task['card_type'] // The substitution will be done in JS format_string_recursive function
  ));

  $nbTask = self::getUniqueValueFromDB( "SELECT count(*) FROM task");
  if($nbTask < $mission['tasks'])
  {
  $this->addOneTask();
  $this->gamestate->nextState( 'next' );
  }
  else
  {
  $this->gamestate->nextState( 'trick' );
  }
  return;

  }
  }
*/

}
