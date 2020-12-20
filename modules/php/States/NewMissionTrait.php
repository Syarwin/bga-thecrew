<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\Cards;
use CREW\Tasks;
use CREW\Missions;

/*
 * Handle start of new mission
 */
trait NewMissionTrait
{
  public function stPreparation()
  {
    Cards::clearMission();
    Tasks::clearMission();
    Players::clearMission();

    // Start new mission
    Globals::startNewMission();
    $mission = Missions::getCurrentId();
    Notifications::message(clienttranslate('Start new mission ${mission}'), [
      'mission' => $mission,
    ]);

    // Deal new cards
    $commanderId = Cards::startNewMission();
    $commander = Players::get($commanderId);

    /*
            self::notifyPlayer($player_id, 'commpending', '', array(
                'pending' => 0,
                'canCommunicate' => 1
            ));
    */

/*
      self::notifyAllPlayers('cleanUp', '', array(
          'mission' => $missionnb,
          'mission_attempts' => self::getUniqueValueFromDB( "SELECT attempt FROM logbook where mission=".$missionnb),// NOI18N;
          'total_attempts' => self::getUniqueValueFromDB( "SELECT sum(attempt) FROM logbook"),
          'distress' => self::getUniqueValueFromDB( "SELECT distress FROM logbook where mission=".$missionnb),
          'players'=> $players
      ));
*/

    // Notify player about commander
    Globals::setCommander($commanderId);
    Notifications::newCommander($commander);
    $this->gamestate->changeActivePlayer($commanderId);

    // Prepare new mission and go to corresponding state
    $mission = Missions::getCurrent();
    $mission->prepare();
    $state = $mission->getStartingState();
    $this->gamestate->nextState($state);
  }
}
