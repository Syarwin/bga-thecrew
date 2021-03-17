<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\Cards;
use CREW\Tasks;
use CREW\Missions;
use CREW\LogBook;

/*
 * Handle start and end of mission
 */
trait MissionTrait
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
    Notifications::cleanUp();

    // Notify player about commander
    Globals::setCommander($commanderId);
    Notifications::newCommander($commander);
    $this->gamestate->changeActivePlayer($commanderId);

    // Prepare new mission and go to corresponding state
    $mission = Missions::getCurrent();
    $mission->prepare();
    $state = $mission->getStartingState();
    if($state == 'question')
      $this->activeNextPlayer();

    $this->gamestate->nextState($state);
  }


  /************************
  ****** END MISSION ******
  ************************/
  function argEndMission()
  {
    return [
      'end' => Globals::getMissionFinished(),
      'number' => Missions::getCurrentId(),
    ];
  }

  function actContinueMissions()
  {
    Notifications::continueMissions();
    $this->gamestate->setPlayerNonMultiactive(self::getCurrentPlayerId(), "next");
  }

  function actStopMissions()
  {
    Globals::setEndOfGame();
    Notifications::stopMissions();
    $this->gamestate->nextState("next");
  }



  function stChangeMission()
  {
    $missionId = Missions::getCurrentId();
    if(Globals::getMissionFinished() > 0){
      $missionId++;
      LogBook::startMission($missionId);
      if($missionId == 51){
        $this->gamestate->nextState('save');
        return;
      }
    } else {
      LogBook::newAttempt($missionId);
    }


    if($missionId > 10 && !Globals::isPremium()){
      Notifications::noPremium();
      $this->gamestate->nextState('end');
    } else {
      Globals::setMissionFinished(0);
      $newState = Globals::isEndOfGame()? 'end' : 'next';
      $this->gamestate->nextState($newState);
    }
  }

  function stSave()
  {
    if(Globals::isCampaign()) {
      $logs = self::getCollectionFromDb("SELECT mission, attempt, success, distress FROM logbook");
      $json = json_encode ($logs);
      $this->storeLegacyTeamData($json);
    }
    $this->gamestate->nextState('next');
  }
}
