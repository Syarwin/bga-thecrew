<?php

/*
 * State constants
 */
define("STATE_SETUP",1);
define("STATE_PREPARATION",2);
define("STATE_PLAYERTURN",3);
define("STATE_NEW_TRICK",4);
define("STATE_PICK_TASK",5);
define("STATE_NEXT_PLAYER_PICK_TASK",6);
define("STATE_NEXTPLAYER",7);
define("STATE_ENDMISSION",8);
define("STATE_COMM",9);
define("STATE_BEFORECOMM",10);
define("STATE_AFTERCOMM",11);
define("STATE_COMM_TOKEN",12);
define("STATE_CHANGE_MISSION",13);
define("STATE_DISTRESS_SETUP",14);
define("STATE_DISTRESS",15);
define("STATE_DISTRESS_EXCHANGE",16);
define("STATE_QUESTION",17);
define("STATE_NEXTQUESTION",18);
define("STATE_PICK_CREW",19);
define("STATE_MULTI_SELECT",20);
define("STATE_SAVE",21);
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
 * COLORS
 */
define("CARD_BLUE", 1);
define("CARD_GREEN", 2);
define("CARD_PINK", 3);
define("CARD_YELLOW", 4);
define("CARD_ROCKET", 5);
define("COMMUNICATION", 6);

// TODO remove
define("COMM",6);
