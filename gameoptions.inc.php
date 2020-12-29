<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * The Crew implementation : © Nicolas Gocel <nicolas.gocel@gmail.com> & Timothée Pecatte <tim.pecatte@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * gameoptions.inc.php
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

include_once("modules/php/constants.inc.php");

$game_options = [
  OPTION_MISSION => [
    'name' => totranslate('Mission'),
    'values' => [
      CAMPAIGN => ['name' => totranslate('Campaign'), 'tmdisplay' => totranslate('Continue existing campaign or start a new one') ],
      1 => ['name' => totranslate('Mission').' 1'],
      2 => ['name' => totranslate('Mission').' 2'],
      3 => ['name' => totranslate('Mission').' 3'],
      4 => ['name' => totranslate('Mission').' 4'],
      5 => ['name' => totranslate('Mission').' 5'],
      6 => ['name' => totranslate('Mission').' 6'],
      7 => ['name' => totranslate('Mission').' 7'],
      8 => ['name' => totranslate('Mission').' 8'],
      9 => ['name' => totranslate('Mission').' 9'],
      10 => ['name' => totranslate('Mission').' 10'],
      11 => ['name' => totranslate('Mission').' 11','premium' => true],
      12 => ['name' => totranslate('Mission').' 12','premium' => true],
      13 => ['name' => totranslate('Mission').' 13','premium' => true],
      14 => ['name' => totranslate('Mission').' 14','premium' => true],
      15 => ['name' => totranslate('Mission').' 15','premium' => true],
      16 => ['name' => totranslate('Mission').' 16','premium' => true],
      17 => ['name' => totranslate('Mission').' 17','premium' => true],
/*
      18 => ['name' => totranslate('Mission').' 18','premium' => true],
      19 => ['name' => totranslate('Mission').' 19','premium' => true],
      20 => ['name' => totranslate('Mission').' 20','premium' => true],
      21 => ['name' => totranslate('Mission').' 21','premium' => true],
      22 => ['name' => totranslate('Mission').' 22','premium' => true],
      23 => ['name' => totranslate('Mission').' 23','premium' => true],
      24 => ['name' => totranslate('Mission').' 24','premium' => true],
      25 => ['name' => totranslate('Mission').' 25','premium' => true],
      26 => ['name' => totranslate('Mission').' 26','premium' => true],
      27 => ['name' => totranslate('Mission').' 27','premium' => true],
      28 => ['name' => totranslate('Mission').' 28','premium' => true],
      29 => ['name' => totranslate('Mission').' 29','premium' => true],
      30 => ['name' => totranslate('Mission').' 30','premium' => true],
      31 => ['name' => totranslate('Mission').' 31','premium' => true],
      32 => ['name' => totranslate('Mission').' 32','premium' => true],
      33 => ['name' => totranslate('Mission').' 33','premium' => true],
      34 => ['name' => totranslate('Mission').' 34','premium' => true],
      35 => ['name' => totranslate('Mission').' 35','premium' => true],
      36 => ['name' => totranslate('Mission').' 36','premium' => true],
      37 => ['name' => totranslate('Mission').' 37','premium' => true],
      38 => ['name' => totranslate('Mission').' 38','premium' => true],
      39 => ['name' => totranslate('Mission').' 39','premium' => true],
      40 => ['name' => totranslate('Mission').' 40','premium' => true],
      41 => ['name' => totranslate('Mission').' 41','premium' => true],
      42 => ['name' => totranslate('Mission').' 42','premium' => true],
      43 => ['name' => totranslate('Mission').' 43','premium' => true],
      44 => ['name' => totranslate('Mission').' 44','premium' => true],
      45 => ['name' => totranslate('Mission').' 45','premium' => true],
      46 => ['name' => totranslate('Mission').' 46','premium' => true],
      47 => ['name' => totranslate('Mission').' 47','premium' => true],
      48 => ['name' => totranslate('Mission').' 48','premium' => true],
      49 => ['name' => totranslate('Mission').' 49','premium' => true],
      50 => ['name' => totranslate('Mission').' 50','premium' => true],
      */
    ]
  ],

  OPTION_CHALLENGE => [
    'name' => totranslate('Challenge mode for Three'),
    'values' => [
      CHALLENGE_OFF => [ 'name' => totranslate('Off')],
      CHALLENGE_ON => [ 'name' => totranslate('On')],
    ],
    'startcondition' => [
      2 => [
        [
        'type' => 'maxplayers',
        'value' => 3,
        'message' => totranslate('Challenge mode for Three is only for 3 players.'),
        'gamestartonly' => true
        ],
      ]
    ]
  ],
];
