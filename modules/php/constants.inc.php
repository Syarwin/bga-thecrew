<?php

/*
 * State constants
 */
define("STATE_SETUP",1);
define("STATE_PREPARATION",2);

define("STATE_PICK_TASK",3);
define("STATE_NEXT_PLAYER_PICK_TASK",4);

define("STATE_QUESTION",5);
define("STATE_NEXT_QUESTION",6);

define("STATE_PICK_CREW",7);
define("STATE_MULTI_SELECT",8);

define("STATE_NEW_TRICK",9);

define("STATE_DISTRESS_SETUP",10);
define("STATE_DISTRESS",11);
define("STATE_DISTRESS_EXCHANGE",12);

define("STATE_BEFORE_COMM",13);
define("STATE_COMM",14);

define("STATE_PLAYER_TURN",15);
define("STATE_NEXT_PLAYER",16);

define("STATE_END_MISSION",17);
define("STATE_CHANGE_MISSION",18);

define("STATE_SAVE",19);
define("STATE_END_OF_GAME",99);


/*
 * Game options
 */
define('OPTION_MISSION', 100);
define('CAMPAIGN', 999);


define('OPTION_CHALLENGE', 101);
define('CHALLENGE_OFF', 1);
define('CHALLENGE_ON', 2);

/*
 * DISTRESS DIRECTION
 */
define('CLOCKWISE', 1);
define('DONT_USE', 2);
define('ANTICLOCKWISE', 3);

/*
 * COLORS
 */
define("CARD_BLUE", 1);
define("CARD_GREEN", 2);
define("CARD_PINK", 3);
define("CARD_YELLOW", 4);
define("CARD_ROCKET", 5);
define("COMMUNICATION", 6);


/*
 * MISSION STATUS
 */
define('MISSION_FAIL', -1);
define('MISSION_CONTINUE', 0);
define('MISSION_SUCCESS', 1);
