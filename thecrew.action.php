<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * The Crew implementation : © Nicolas Gocel <nicolas.gocel@gmail.com> & Timothée Pecatte <tim.pecatte@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * thecrew.action.php
 *
 * thecrew main action entry point
 *
 */


class action_thecrew extends APP_GameAction
{
  // Constructor: please do not modify
  public function __default()
  {
    if( self::isArg( 'notifwindow') ) {
      $this->view = "common_notifwindow";
      $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
    } else {
      $this->view = "thecrew_thecrew";
      self::trace( "Complete reinitialization of board game" );
    }
  }


  public function setAutopick()
  {
    self::setAjaxMode();
    $mode = self::getArg("autopick", AT_posint, true);
    $this->game->actSetAutopick($mode);
    self::ajaxResponse();
  }

  public function setAutocontinue()
  {
    self::setAjaxMode();
    $mode = self::getArg("autocontinue", AT_posint, true);
    $this->game->actSetAutocontinue($mode);
    self::ajaxResponse();
  }



  public function actMoveTile()
  {
    self::setAjaxMode();
    $id1 = self::getArg("taskId1", AT_posint, true);
    $id2 = self::getArg("taskId2", AT_posint, true);
    $this->game->actMoveTile($id1 ,$id2);
    self::ajaxResponse();
  }

  public function actPassMoveTile()
  {
    self::setAjaxMode();
    $this->game->actPassMoveTile();
    self::ajaxResponse();
  }


  public function actChooseTask()
  {
    self::setAjaxMode();
    $taskId = self::getArg("taskId", AT_posint, true);
    $this->game->actChooseTask($taskId);
    self::ajaxResponse();
  }

  public function actPlayCard()
  {
    self::setAjaxMode();
    $cardId = self::getArg("cardId", AT_posint, true);
    $this->game->actPlayCard( $cardId);
    self::ajaxResponse( );
  }


  public function actPreselectCard()
  {
    self::setAjaxMode();
    $cardId = self::getArg("cardId", AT_posint, true);
    $this->game->actPreselectCard($cardId);
    self::ajaxResponse( );
  }

  public function setRestartMission()
  {
    self::setAjaxMode();
    $this->game->actRestartMission();
    self::ajaxResponse( );
  }


  public function actContinueMissions()
  {
    self::setAjaxMode();
    $this->game->actContinueMissions();
    self::ajaxResponse( );
  }

  public function actStopMissions()
  {
    self::setAjaxMode();
    $this->game->actStopMissions();
    self::ajaxResponse( );
  }


/***********************
******* QUESTION *******
***********************/
  public function actReply()
  {
    self::setAjaxMode();
    $reply = self::getArg("reply", AT_posint, true );
    $this->game->actReply($reply);
    self::ajaxResponse();
  }


/***********************
******* DISTRESS *******
***********************/
  public function actChooseDirection()
  {
    self::setAjaxMode();
    $dir = self::getArg("dir", AT_posint, true);
    $this->game->actChooseDirection($dir);
    self::ajaxResponse();
  }

  public function actChooseCardDistress()
  {
    self::setAjaxMode();
    $cardId = self::getArg("cardId", AT_posint, true);
    $this->game->actChooseCardDistress($cardId);
    self::ajaxResponse( );
  }

  public function actChooseCardDistressJarvis()
  {
    self::setAjaxMode();
    $cardId = self::getArg('cardId', AT_posint, true);
    $jarvisCardId = self::getArg('jarvisCardId', AT_posint, true);
    $this->game->actChooseCardDistressJarvis($cardId, $jarvisCardId);
    self::ajaxResponse();
  }


/***********************
***** COMMUNICATION ****
***********************/

  public function actToggleComm()
  {
    self::setAjaxMode();
    $this->game->actToggleComm();
    self::ajaxResponse( );
  }

  public function actCancelComm()
  {
    self::setAjaxMode();
    $this->game->actCancelComm();
    self::ajaxResponse( );
  }

  public function actConfirmComm()
  {
    self::setAjaxMode();
    $cardId = self::getArg("cardId", AT_posint, true );
    $status = self::getArg("status", AT_alphanum, true );
    $this->game->actConfirmComm($cardId, $status);
    self::ajaxResponse();
  }



  public function actPickCrew()
  {
    self::setAjaxMode();
    $crewId = self::getArg("pId", AT_posint, true );
    $this->game->actPickCrew( $crewId);
    self::ajaxResponse();
  }


/************************
**** 5 players rule *****
************************/
  public function actGiveTask()
  {
    self::setAjaxMode();
    $taskId = self::getArg("taskId", AT_posint, true);
    $pId = self::getArg("pId", AT_posint, true);
    $this->game->actGiveTask($taskId, $pId);
    self::ajaxResponse();
  }

  public function actPassGiveTask()
  {
    self::setAjaxMode();
    $this->game->actPassGiveTask();
    self::ajaxResponse( );
  }


  public function actConfirmGiveTask()
  {
    self::setAjaxMode();
    $this->game->actConfirmGiveTask();
    self::ajaxResponse( );
  }

  public function actRejectGiveTask()
  {
    self::setAjaxMode();
    $this->game->actRejectGiveTask();
    self::ajaxResponse( );
  }

}
