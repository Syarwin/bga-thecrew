<?php
namespace CREW\States;
use CREW\Game\Globals;
use CREW\Game\GlobalsVars;
use CREW\Game\Players;
use CREW\Game\Notifications;
use CREW\Missions;
use CREW\Helpers\Utils;
use CREW\LogBook;
use CREW\Cards;

/*
 * Handle restart mission
 */
trait RestartMissionTrait
{
  function stPreRestartMission()
  {
    $answers = Players::getAllRestartMissionAnswers();
    $needVote = (count($answers) > 1 || (isset($answers[0]) && $answers[0] == 0));
    $this->gamestate->nextState($needVote? 'setup' : 'turn');
  }



  function stRestartMissionSetup()
  {
    $this->gamestate->setAllPlayersMultiactive();
  }

  function argRestartMissionSetup()
  {
    return [
      'players' => Players::getAllRestartMissionAnswersAssoc()
    ];
  }

  function isEveryOneWantingRestartMission($answers) {
    return $answers === array_filter($answers, function ($answer) use ($answers) {
        return ($answer === WANT_FAIL_MISSION);
    });
  }

  function actAnswerRestartMission($answer)
  {
    $player = Players::getCurrent();
    $player->answerRestartMission($answer);
    Notifications::chooseRestartMission($player, $answer);

    $answers = Players::getAllRestartMissionAnswers();

    if ($answer == DONT_WANT_FAIL_MISSION || in_array(DONT_WANT_FAIL_MISSION, $answers)) {
      // Someone doesn't want to restart mission, go back to game
      Players::clearRestartMissionAnswers();
      $this->gamestate->nextState('cancel');
    }

    if ($this->isEveryOneWantingRestartMission($answers)) {
      $currentMission = Missions::getCurrent();
      $missionId = $currentMission->getId();
      $player = Players::getCurrent();
      Notifications::restartMissionActivated($missionId);
      Players::clearRestartMissionAnswers();
      $this->gamestate->nextState('endMission');
    } else {
      // pause time for player that answered
      $this->gamestate->setPlayerNonMultiactive($player->getId(), '');
    }

  }
}
