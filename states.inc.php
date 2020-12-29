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

/***********************
******* QUESTION *******
***********************/
  STATE_QUESTION => [
    "name" => "question",
    "description" => clienttranslate('Commander ${player_name} asks ${actplayer} : ${question}'),
    "descriptionmyturn" => clienttranslate('Commander ${player_name} asks ${you} : ${question}'),
    "type" => "activeplayer",
    "args" => "argQuestion",
    "possibleactions" => ["actReply"],
    "transitions" => [
      "next" => STATE_NEXT_QUESTION,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

  STATE_NEXT_QUESTION => [
    "name" => "nextQuestion",
    "description" => "",
    "type" => "game",
    "action" => "stNextQuestion",
    "transitions" => [
      "next" => STATE_QUESTION,
      "pick" => STATE_PICK_CREW
    ],
  ],


  /**********************
  ****** PICK CREW ******
  **********************/
  STATE_PICK_CREW => [
    "name" => "pickCrew",
    "description" => clienttranslate('${actplayer} must choose a crew member'),
    "descriptionmyturn" => clienttranslate('${you} must choose a crew member'),
    "type" => "activeplayer",
    "args" => "argPickCrew",
    "possibleactions" => ["actPickCrew"],
    "transitions" => [
      "task" => STATE_PICK_TASK,
      "trick" => STATE_NEW_TRICK,
      "next" => STATE_NEXT_QUESTION,
      "pickCrew" => STATE_PICK_CREW,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

/*
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
      "next" => STATE_BEFORE_COMM,
      "distress" => STATE_DISTRESS_SETUP
    ]
  ],


  /***********************
  ****** DISTRESS  *******
  ***********************/
  STATE_DISTRESS_SETUP => [
    "name" => "distressSetup",
    "description" => clienttranslate('The distress signal might be used'),
    "descriptionmyturn" => clienttranslate('${you} may use the distress signal'),
    "type" => "multipleactiveplayer",
    "action" => "stDistressSetup",
    "possibleactions" => ["actChooseDirection"],
    "transitions" => [
      "next" => STATE_DISTRESS,
      "turn" => STATE_BEFORE_COMM,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

  STATE_DISTRESS => [
    "name" => "distress",
    "args" => "argDistress",
    "type" => "multipleactiveplayer",
    "possibleactions" => ["actChooseCardDistress"],
    "description" => clienttranslate('Every players must choose a card to pass'),
    "descriptionmyturn" => clienttranslate('${you} must choose a card to pass'),
    "transitions" => [
      "next" => STATE_DISTRESS_EXCHANGE,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

  STATE_DISTRESS_EXCHANGE => [
    "name" => "distressExchange",
    "description" => "",
    "type" => "game",
    "action" => "stDistressExchange",
    "transitions" => ["next" => STATE_BEFORE_COMM],
  ],




  /****************************
  ****** COMMUNICATION  *******
  ****************************/
  STATE_BEFORE_COMM => [
    "name" => "beforeComm",
    "description" => "",
    "type" => "game",
    "action" => "stBeforeComm",
    "transitions" => [
      "turn" => STATE_PLAYER_TURN,
      "comm" => STATE_COMM
    ]
  ],

  STATE_COMM => [
    "name" => "comm",
    "description" => clienttranslate('${actplayer} must choose a card to communicate'),
    "descriptionmyturn" => clienttranslate('${you} must choose a card to communicate'),
    "type" => "activeplayer",
    "args" => "argComm",
    "possibleactions" => ["actConfirmComm", "actCancelComm"],
    "transitions" => [
      "next" => STATE_BEFORE_COMM,
      "cancel" => STATE_BEFORE_COMM,
      "after" => STATE_BEFORE_COMM,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],


  /************************
  ****** PLAY CARD  *******
  ************************/
  STATE_PLAYER_TURN => [
    "name" => "playerTurn",
    "description" => clienttranslate('${actplayer} must play a card'),
    "descriptionmyturn" => clienttranslate('${you} must play a card'),
    "type" => "activeplayer",
    "args" => "argPlayerTurn",
    "possibleactions" => ["actPlayCard", "actStartComm", "actDistress"],
    "transitions" => [
      "next" => STATE_NEXY_PLAYER,
      "startComm" => STATE_BEFORE_COMM,
      "distress" => STATE_DISTRESS_SETUP,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

  STATE_NEXY_PLAYER => [
    "name" => "nextPlayer",
    "description" => "",
    "type" => "game",
    "action" => "stNextPlayer",
    "transitions" => [
      "nextPlayer" => STATE_PLAYER_TURN,
      "nextTrick" => STATE_NEW_TRICK,
      "endMission" => STATE_END_MISSION
    ],
    "updateGameProgression" => true
  ],

/*************************
****** / END TRICK *******
*************************/




/***********************
************************
**** END OF MISSION ****
************************
***********************/
  STATE_END_MISSION => [
    "name" => "endMission",
    "args" => "argEndMission",
    "type" => "multipleactiveplayer",
    "possibleactions" => ["actButton"],
    "description" => clienttranslate('All players must continue or stop'),
    "descriptionmyturn" => clienttranslate('${you} must continue or stop'),
    "transitions" => [
      "next" => STATE_CHANGE_MISSION,
      "end" => STATE_SAVE,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],


  STATE_CHANGE_MISSION => [
    "name" => "changeMission",
    "description" => "",
    "type" => "game",
    "action" => "stChangeMission",
    "transitions" => [
      "next" => STATE_PREPARATION,
      "save" => STATE_SAVE,
      "end" => STATE_SAVE
    ]
  ],

  STATE_SAVE => [
    "name" => "save",
    "description" => "",
    "type" => "game",
    "action" => "stSave",
    "transitions" => ["next" => STATE_END_OF_GAME]
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
