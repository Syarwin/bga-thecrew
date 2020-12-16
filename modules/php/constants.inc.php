<?php

/*
 * State constants
 */
define('ST_GAME_SETUP', 1);
define('ST_NEXT_PLAYER', 3);
define('ST_START_OF_TURN', 4);
define('ST_PLAY_CARD', 5);
define('ST_AWAIT_REACTION',6);
define('ST_AWAIT_MULTIREACTION',7);
define('ST_REACT', 8);
define('ST_MULTIREACT', 9);
define('ST_END_REACT', 10);
define('ST_END_OF_TURN', 11);
define('ST_DISCARD_EXCESS', 12);
define('ST_DRAW_CARDS', 13);
define('ST_PREPARE_SELECTION', 14);
define('ST_SELECT_CARD', 15);
define('ST_ACTIVE_DRAW_CARD', 17);
define('ST_ELIMINATE', 16);
define('ST_PRE_GAME_END', 98);
define('ST_GAME_END', 99);

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
