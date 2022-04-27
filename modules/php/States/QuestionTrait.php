<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\GlobalsVars;
use CREW\Game\Players;
use CREW\Game\Notifications;
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
    $tasks = Tasks::getUnassigned();
    $mission = Missions::getCurrent();

    return [
      'i18n' => ['question'],
      'player_name' => Players::getCommander()->getName(),
      'question' => $mission->getQuestion(),
      'replies' => $mission->getReplies(),
      'tasks' => $mission->getTasks($tasks),
      'jarvisActive' => GlobalsVars::isJarvisActive(),
    ];
  }

  function actReply($i)
  {
    $replies = Missions::getCurrent()->getReplies();
    $reply = $replies[$i];
    $player = Players::getCurrent();
    $player->reply($i);
    Notifications::speak($player, $reply);
    $this->gamestate->nextState('next');
  }


  function stNextQuestion()
  {
    $this->activeNextPlayer();
    $newState = $this->getActivePlayerId() == Globals::getCommander() && !GlobalsVars::isJarvisActive() ? 'pick' : 'next';
    $this->gamestate->nextState($newState);
  }


  function argPickCrew()
  {
    $tasks = Tasks::getUnassigned();
    $mission = Missions::getCurrent();

    return [
      'players' => $mission->getTargetablePlayers(),
      'tasks' => $mission->getTasks($tasks),
    ];
  }


  function actPickCrew($crewId)
  {
    self::checkAction("actPickCrew");

    // Clear replies
    Players::clearReplies();
    Notifications::clearReplies();

    $player = Players::getCurrent();
    $mission = Missions::getCurrent();
    $newState = $mission->pickCrew($crewId);

    $newState = $newState ?? (count(Tasks::getUnassigned()) > 0? 'task' : 'trick');
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
*/

}
