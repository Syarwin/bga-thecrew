<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * thecrew implementation : © Nicolas Gocel <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * thecrew game states description
 *
 */

//    !! It is not a good idea to modify this file when a game is running !!


$machinestates = [
  // The initial state. Please do not modify.
  STATE_SETUP => [
    "name" => "gameSetup",
    "description" => "",
    "type" => "manager",
    "action" => "stGameSetup",
    "transitions" => [ "" => STATE_PREPARATION ]
  ],

  STATE_PREPARATION => [
    "name" => "preparation",
    "description" => "",
    "type" => "game",
    "action" => "stPreparation",
    "transitions" => [
      "task" => STATE_PICK_TASK,
      "trick" => STATE_NEW_TRICK,
      "question" => STATE_QUESTION,
      "pickCrew" => STATE_PICK_CREW,
      "multiSelect" => STATE_MULTI_SELECT
    ]
  ],


/***********************
****** PICK TASK *******
***********************/

  STATE_PICK_TASK => [
    "name" => "pickTask",
    "description" => clienttranslate('${actplayer} must choose a task'),
    "descriptionmyturn" => clienttranslate('${you} must choose a task'),
    "type" => "activeplayer",
    "args" => "argPickTask",
    "possibleactions" => ["actChooseTask"],
    "transitions" => [
      "next" => STATE_NEXT_PLAYER_PICK_TASK,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

  STATE_NEXT_PLAYER_PICK_TASK => [
    "name" => "checkPickTask",
    "description" => "",
    "type" => "game",
    "action" => "stNextPickTask",
    "transitions" => [
      "task" => STATE_PICK_TASK,
      "turn" => STATE_NEW_TRICK
    ]
  ],

  /*
  /***********************
  ****** QUESTION *******
  ***********************
  STATE_QUESTION => [
    "name" => "question",
    "description" => clienttranslate('Commander ${commander} asks ${actplayer} : ${question}'),
    "descriptionmyturn" => clienttranslate('Commander ${commander} asks ${you} : ${question}'),
    "type" => "activeplayer",
    "args" => "argQuestion",
    "possibleactions" => ["actButton"],
    "transitions" => [
      "next" => STATE_NEXTQUESTION,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

  STATE_NEXTQUESTION => [
    "name" => "nextQuestion",
    "description" => "",
    "type" => "game",
    "action" => "stNextQuestion",
    "transitions" => [
      "next" => STATE_QUESTION,
      "pick" => STATE_PICK_CREW
    ],
  ],

  STATE_PICK_CREW => [
  "name" => "pickCrew",
  "description" => clienttranslate('${actplayer} must choose a crew member'),
  "descriptionmyturn" => clienttranslate('${you} must choose a crew member'),
  "type" => "activeplayer",
  "args" => "argPickCrew",
  "possibleactions" => ["actPickCrew"],
  "transitions" => ["task" => STATE_PICK_TASK, "trick" => STATE_NEW_TRICK, "next"=>STATE_NEXTQUESTION, "pickCrew" => STATE_PICK_CREW, "zombiePass" => STATE_CHANGE_MISSION]
  ],

    STATE_MULTI_SELECT => [
        "name" => "multiSelect",
        "description" => clienttranslate('${actplayer} must do according to your mission'),
        "descriptionmyturn" => clienttranslate('${you} must do according to your mission'),
        "type" => "activeplayer",
        "args" => "argMultiSelect",
        "possibleactions" => ["actMultiSelect", "actCancel"],
        "transitions" => ["same" => STATE_MULTI_SELECT, "cancel" => STATE_PICK_TASK,  "task" => STATE_PICK_TASK, "zombiePass" => STATE_CHANGE_MISSION]
    ],
*/




/***********************
************************
******** TRICK *********
************************
***********************/
  STATE_NEW_TRICK => [
    "name" => "newTrick",
    "description" => "",
    "type" => "game",
    "action" => "stNewTrick",
    "transitions" => [
      "next" => STATE_BEFORECOMM,
      "distress" => STATE_DISTRESS_SETUP
    ]
  ],

  /***********************
  ****** DISTRESS  *******
  ***********************
    STATE_DISTRESS_SETUP => [
        "name" => "distressSetup",
        "description" => clienttranslate('Distress signal : ${actplayer} must decide where to pass the cards'),
        "descriptionmyturn" => clienttranslate('Distress signal : ${you} must decide where to pass the cards'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => ["actButton"],
        "transitions" => ["next" => STATE_DISTRESS, "turn" => STATE_BEFORECOMM, "zombiePass" => STATE_CHANGE_MISSION]
    ],

    STATE_DISTRESS => [
        "name" => "distress",
        "args" => "argDistress",
        "type" => "multipleactiveplayer",
        "possibleactions"       => ["actPlayCard"],
        "description" => clienttranslate('Every players must choose a card to pass'),
        "descriptionmyturn" => clienttranslate('${you} must choose a card to pass'),
        "transitions" => ["next" => STATE_DISTRESS_EXCHANGE, "zombiePass" => STATE_CHANGE_MISSION]
    ],

    STATE_DISTRESS_EXCHANGE => [
        "name" => "distressExchange",
        "description" => "",
        "type" => "game",
        "action" => "stDistressExchange",
        "transitions" => ["next" => STATE_BEFORECOMM],
    ],*/



  STATE_BEFORECOMM => [
    "name" => "beforeComm",
    "description" => "",
    "type" => "game",
    "action" => "stBeforeComm",
    "transitions" => [
      "turn" => STATE_PLAYERTURN,
      "comm" => STATE_COMM
    ]
  ],

  STATE_PLAYERTURN => [
    "name" => "playerTurn",
    "description" => clienttranslate('${actplayer} must play a card'),
    "descriptionmyturn" => clienttranslate('${you} must play a card'),
    "type" => "activeplayer",
    "args" => "argPlayerTurn",
    "possibleactions" => ["actPlayCard", "actStartComm", "actDistress"],
    "transitions" => [
      "next" => STATE_NEXTPLAYER,
      "startComm" => STATE_BEFORECOMM,
      "distress" => STATE_DISTRESS_SETUP,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

  STATE_NEXTPLAYER => [
    "name" => "nextPlayer",
    "description" => "",
    "type" => "game",
    "action" => "stNextPlayer",
    "transitions" => [
      "nextPlayer" => STATE_PLAYERTURN,
      "nextTrick" => STATE_NEW_TRICK,
      "endMission" => STATE_ENDMISSION
    ],
    "updateGameProgression" => true
  ],


    STATE_COMM => [
        "name" => "comm",
        "description" => clienttranslate('${actplayer} must choose a card to communicate'),
        "descriptionmyturn" => clienttranslate('${you} must choose a card to communicate'),
        "type" => "activeplayer",
        "args" => "argComm",
        "possibleactions" => ["actPlayCard", "actCancel"],
        "transitions" => ["next" => STATE_COMM_TOKEN, "cancel" => STATE_AFTERCOMM, "after" => STATE_AFTERCOMM, "zombiePass" => STATE_CHANGE_MISSION]
    ],

    STATE_COMM_TOKEN => [
        "name" => "commToken",
        "description" => clienttranslate('${actplayer} must place its communication token'),
        "descriptionmyturn" => clienttranslate('${you} must place your communication token'),
        "type" => "activeplayer",
        "args" => "argCommToken",
        "possibleactions" => ["actFinishComm"],
        "transitions" => ["next" => STATE_AFTERCOMM, "zombiePass" => STATE_CHANGE_MISSION]
    ],

    STATE_AFTERCOMM => [
        "name" => "afterComm",
        "description" => "",
        "type" => "game",
        "action" => "stAfterComm",
        "transitions" => ["next" => STATE_BEFORECOMM]
    ],

    STATE_ENDMISSION => [
        "name" => "endMission",
        "args" => "argEndMission",
        "type" => "multipleactiveplayer",
        "possibleactions"       => ["actButton"],
        "description" => clienttranslate('All players must continue or stop'),
        "descriptionmyturn" => clienttranslate('${you} must continue or stop'),
        "transitions" => ["next" => STATE_CHANGE_MISSION, "end" => STATE_SAVE, "zombiePass" => STATE_CHANGE_MISSION]
    ],

    STATE_CHANGE_MISSION => [
        "name" => "changeMission",
        "description" => "",
        "type" => "game",
        "action" => "stChangeMission",
        "transitions" => ["next" => STATE_PREPARATION, "save" => STATE_SAVE, "end" => STATE_SAVE]
    ],

    STATE_SAVE => [
        "name" => "save",
        "description" => "",
        "type" => "game",
        "action" => "stSave",
        "transitions" => ["next" => 99]
    ],

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    STATE_END_OF_GAME => [
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ]

];
