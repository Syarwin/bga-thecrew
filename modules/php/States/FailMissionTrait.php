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
 * Handle fail mission
 */
trait FailMissionTrait
{
  function stPreFailMission()
  {
    $answers = Players::getAllFailMissionAnswers();
    $needVote = (count($answers) > 1 || (isset($answers[0]) && $answers[0] == 0));
    $this->gamestate->nextState($needVote? 'setup' : 'turn');
  }



  function stFailMissionSetup()
  {
    $this->gamestate->setAllPlayersMultiactive();
  }

  function argFailMissionSetup()
  {
    return [
      'players' => Players::getAllFailMissionAnswersAssoc()
    ];
  }

  function isEveryOneWantingFailMission($answers) {
    return $answers === array_filter($answers, function ($answer) use ($answers) {
        return ($answer === WANT_FAIL_MISSION);
    });
  }

  function actAnswerRestartMission($answer)
  {
    $player = Players::getCurrent();
    $player->answerFailMission($answer);
    Notifications::chooseFailMission($player, $answer);

    $answers = Players::getAllFailMissionAnswers();

    if ($answer == DONT_WANT_FAIL_MISSION || in_array(DONT_WANT_FAIL_MISSION, $answers)) {
      // Someone doesn't want to fail mission, go back to game
      Players::clearFailMissionAnswers();
      $this->gamestate->nextState('cancel');
    }

    if ($this->isEveryOneWantingFailMission($answers)) {
      $currentMission = Missions::getCurrent();
      $missionId = $currentMission->getId();
      $player = Players::getCurrent();
      Notifications::failMissionActivated($missionId);
      Players::clearFailMissionAnswers();
      $this->gamestate->nextState('endMission');
    }

  }
}
