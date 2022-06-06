<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\GlobalsVars;
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
    GlobalsVars::setJarvisTricks(0);
    GlobalsVars::setJarvisReply(0);

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
  function stPreEndMission()
  {
    $players = [];
    foreach(Players::getAll() as $player){
      if($player->getContinueAuto() == DISABLED)
        $players[] = $player->getId();
    }

    if(empty($players)){
      $this->gamestate->nextState('next');
    } else {
      $this->gamestate->setPlayersMultiactive($players, "next", true);
      $this->gamestate->nextState('pending');
    }
  }


  function argEndMission()
  {
    return [
      'end' => Globals::getMissionFinished(),
      'number' => Missions::getCurrentId(),
    ];
  }

  function actContinueMissions()
  {
    self::checkAction("actContinueMissions");
    Notifications::continueMissions();
    $this->gamestate->setPlayerNonMultiactive(self::getCurrentPlayerId(), "next");
  }

  function actStopMissions()
  {
    $this->gamestate->checkPossibleAction('actStopMissions');
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
      $result = [];
      foreach($logs as $log)
        $result[] = [$log['mission'], $log['attempt'], $log['success'], $log['distress']];

      $json = json_encode($result);

      try {
        $this->storeLegacyTeamData($json);
      } catch( \feException $e ) {
        // ignore storeLegacyData: cannot store more than 64k of legacy data for player
        if ($e->getCode() != FEX_legacy_size_exceeded ) {
          throw $e;
        } else {
          $this->removeLegacyTeamData();

          // save only last item, so they can still continue, but ignoring logbook
          $json = json_encode([end($result)]);
          $this->storeLegacyTeamData($json);
        }
      }
    }

    $this->gamestate->nextState('next');
  }


  function actRestartMission() {
    $this->gamestate->checkPossibleAction("actRestartMission");
    $currentMission = Missions::getCurrent();
    $missionId = $currentMission->getId();
    $player = Players::getCurrent();
    Notifications::message(clienttranslate('${player_name} wants to restart mission ${mission}'), [
      'player_name' => $player->getName(),
      'mission' => $missionId,
    ]);
    $this->gamestate->nextState("startRestartMission");
  }


  /***********************
  ******* AUTOANSWER *******
  ***********************/
  public function actSetAutocontinue($mode)
  {
    $player = Players::getCurrent();
    $player->setAutoContinue($mode);
  }

}
