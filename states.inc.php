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
      "moveTile" => STATE_MOVE_TILE
    ]
  ],

  STATE_MOVE_TILE => [
    "name" => "moveTile",
    "description" => clienttranslate('${actplayer} may swap the position of two task tokens'),
    "descriptionmyturn" => clienttranslate('${you} may swap the position of two task tokens'),
    "type" => "activeplayer",
    "args" => "argMoveTile",
    "possibleactions" => ["actMoveTile", "actPassMoveTile"],
    "transitions" => [
      "task" => STATE_PICK_TASK,
      "zombiePass" => STATE_CHANGE_MISSION
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
      "distress" => STATE_DISTRESS_SETUP,
      "giveTask" => STATE_GIVE_TASK,
    ]
  ],


  /***********************
  ****** GIVE TASK  ******
  ***********************/
  STATE_GIVE_TASK => [
    "name" => "giveTask",
    "description" => clienttranslate('One crew member may propose to give one of its task to someone else'),
    "descriptionmyturn" => clienttranslate('${you} may propose to give one of your task to someone else'),
    "type" => "multipleactiveplayer",
    "action" => "stGiveTask",
    "args" => "argGiveTask",
    "possibleactions" => ["actGiveTask", "actPassGiveTask"],
    "transitions" => [
      'pass' => STATE_DISTRESS_SETUP,
      'askConfirmation' => STATE_GIVE_TASK_CONFIRMATION,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

  STATE_GIVE_TASK_CONFIRMATION => [
    "name" => "giveTaskConfirmation",
    "description" => clienttranslate('The crew must confirm/reject ${player_name}\'s proposal'),
    "descriptionmyturn" => clienttranslate('${you} must confirm/reject ${player_name}\'s proposal'),
    "type" => "multipleactiveplayer",
    "action" => "stGiveTaskConfirmation",
    "args" => "argGiveTaskConfirmation",
    "possibleactions" => ["actConfirmGiveTask", "actRejectGiveTask"],
    "transitions" => [
      'next' => STATE_GIVE_TASK_EXCHANGE,
      'reject' => STATE_GIVE_TASK,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],


  STATE_GIVE_TASK_EXCHANGE => [
    "name" => "giveTaskExchange",
    "description" => "",
    "type" => "game",
    "action" => "stGiveTaskExchange",
    "transitions" => ["next" => STATE_DISTRESS_SETUP],
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
      "next" => STATE_NEXT_PLAYER,
      "startComm" => STATE_BEFORE_COMM,
      "distress" => STATE_DISTRESS_SETUP,
      "zombiePass" => STATE_CHANGE_MISSION
    ]
  ],

  STATE_NEXT_PLAYER => [
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
