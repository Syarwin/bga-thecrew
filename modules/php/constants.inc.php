<?php

/*
 * State constants
 */
define("STATE_SETUP",1);
define("STATE_PREPARATION",2);

define("STATE_PICK_TASK",5);
define("STATE_NEXT_PLAYER_PICK_TASK",30);

define("STATE_QUESTION",17);
define("STATE_NEXT_QUESTION",18);

define("STATE_PICK_CREW",19);
define("STATE_MOVE_TILE",20);

define("STATE_NEW_TRICK",4);


define("STATE_PRE_DISTRESS",35);
define("STATE_DISTRESS_SETUP",14);
define("STATE_DISTRESS",15);
define("STATE_DISTRESS_EXCHANGE",16);

define('STATE_PRE_GIVE_TASK', 37);
define("STATE_GIVE_TASK", 31); // 5 players rule
define("STATE_GIVE_TASK_CONFIRMATION", 32);
define("STATE_GIVE_TASK_EXCHANGE", 33);

define("STATE_BEFORE_COMM",10);
define("STATE_COMM",9);
define('STATE_USELESS_NOW', 12);

define("STATE_PLAYER_TURN",3);
define("STATE_NEXT_PLAYER",7);

define("STATE_PRE_RESTART_MISSION", 38);
define("STATE_RESTART_MISSION_SETUP", 39);
define("STATE_RESTART_MISSION", 40);
define("STATE_RESTART_MISSION_EXCHANGE", 41);

define("STATE_PRE_END_MISSION",36);
define("STATE_END_MISSION",8);
define("STATE_CHANGE_MISSION",13);

define("STATE_SAVE",21);
define("STATE_END_OF_GAME",99);


/*
 * Game options
 */
define('OPTION_MISSION', 100);
define('CAMPAIGN', 999);
define('NEW_CAMPAIGN', 998);


define('OPTION_CHALLENGE', 101);
define('CHALLENGE_OFF', 1);
define('CHALLENGE_ON', 2);


define('OPTION_TIME', 102);
define('TIME_INF', 1);
define('TIME_SHORT', 2);
define('TIME_MED', 3);
define('TIME_LONG', 4);


define('OPTION_JOURNEY', 103);
define('FULL_JOURNEY', 1);
define('SINGLE_MISSION', 2);
define('SHORT_JOURNEY', 3);
define('MEDIUM_JOURNEY', 4);
define('LONG_JOURNEY', 5);

define('OPTION_SEE_DISCARD', 104);
define('OPTION_SEE_DISCARD_OFF', 0);
define('OPTION_SEE_DISCARD_ON', 1);

define('OPTION_JARVIS', 105);
define('OPTION_JARVIS_OFF', 0);
define('OPTION_JARVIS_ON', 1);

/*
 * DISTRESS DIRECTION
 */
define('CLOCKWISE', 1);
define('DONT_USE', 2);
define('ANTICLOCKWISE', 3);
define('WHATEVER', 4);


define('DISABLED', 0);
define('ALWAYS_NO', 2);
define('ALWAYS_AGREE', 4);

/*
 * COLORS
 */
define("CARD_BLUE", 1);
define("CARD_GREEN", 2);
define("CARD_PINK", 3);
define("CARD_YELLOW", 4);
define("CARD_ROCKET", 5);
define("COMMUNICATION", 6);
define("CARD_HIDDEN", 7);

/**
 * MISSION RESTART
 */

define("WANT_FAIL_MISSION", 1);
define("DONT_WANT_FAIL_MISSION", 2);


/*
 * MISSION STATUS
 */
define('MISSION_FAIL', -1);
define('MISSION_CONTINUE', 0);
define('MISSION_SUCCESS', 1);


/*
 * 2 player mode (Jarvis)
 */
define('JARVIS_ID', 1);
