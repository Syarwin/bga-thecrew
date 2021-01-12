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
      CAMPAIGN => ['name' => totranslate('Campaign'), 'description' => totranslate('Continue existing campaign or start a new one'), 'tmdisplay' => totranslate('Campaing') ],
      1 => ['name' => totranslate('Mission').' 1', 'description' => totranslate('Starting at mission'). ' 1', 'tmdisplay' => totranslate('Starting at mission'). ' 1'],
      2 => ['name' => totranslate('Mission').' 2', 'description' => totranslate('Starting at mission'). ' 2', 'tmdisplay' => totranslate('Starting at mission'). ' 2'],
      3 => ['name' => totranslate('Mission').' 3', 'description' => totranslate('Starting at mission'). ' 3', 'tmdisplay' => totranslate('Starting at mission'). ' 3'],
      4 => ['name' => totranslate('Mission').' 4', 'description' => totranslate('Starting at mission'). ' 4', 'tmdisplay' => totranslate('Starting at mission'). ' 4'],
      5 => ['name' => totranslate('Mission').' 5', 'description' => totranslate('Starting at mission'). ' 5', 'tmdisplay' => totranslate('Starting at mission'). ' 5'],
      6 => ['name' => totranslate('Mission').' 6', 'description' => totranslate('Starting at mission'). ' 6', 'tmdisplay' => totranslate('Starting at mission'). ' 6'],
      7 => ['name' => totranslate('Mission').' 7', 'description' => totranslate('Starting at mission'). ' 7', 'tmdisplay' => totranslate('Starting at mission'). ' 7'],
      8 => ['name' => totranslate('Mission').' 8', 'description' => totranslate('Starting at mission'). ' 8', 'tmdisplay' => totranslate('Starting at mission'). ' 8'],
      9 => ['name' => totranslate('Mission').' 9', 'description' => totranslate('Starting at mission'). ' 9', 'tmdisplay' => totranslate('Starting at mission'). ' 9'],
      10 => ['name' => totranslate('Mission').' 10', 'description' => totranslate('Starting at mission'). ' 10', 'tmdisplay' => totranslate('Starting at mission'). ' 10'],
      11 => ['name' => totranslate('Mission').' 11', 'description' => totranslate('Starting at mission'). ' 11', 'tmdisplay' => totranslate('Starting at mission'). ' 11', 'premium' => true],
      12 => ['name' => totranslate('Mission').' 12', 'description' => totranslate('Starting at mission'). ' 12', 'tmdisplay' => totranslate('Starting at mission'). ' 12', 'premium' => true],
      13 => ['name' => totranslate('Mission').' 13', 'description' => totranslate('Starting at mission'). ' 13', 'tmdisplay' => totranslate('Starting at mission'). ' 13', 'premium' => true],
      14 => ['name' => totranslate('Mission').' 14', 'description' => totranslate('Starting at mission'). ' 14', 'tmdisplay' => totranslate('Starting at mission'). ' 14', 'premium' => true],
      15 => ['name' => totranslate('Mission').' 15', 'description' => totranslate('Starting at mission'). ' 15', 'tmdisplay' => totranslate('Starting at mission'). ' 15', 'premium' => true],
      16 => ['name' => totranslate('Mission').' 16', 'description' => totranslate('Starting at mission'). ' 16', 'tmdisplay' => totranslate('Starting at mission'). ' 16', 'premium' => true],
      17 => ['name' => totranslate('Mission').' 17', 'description' => totranslate('Starting at mission'). ' 17', 'tmdisplay' => totranslate('Starting at mission'). ' 17', 'premium' => true],
      18 => ['name' => totranslate('Mission').' 18', 'description' => totranslate('Starting at mission'). ' 18', 'tmdisplay' => totranslate('Starting at mission'). ' 18', 'premium' => true],
      19 => ['name' => totranslate('Mission').' 19', 'description' => totranslate('Starting at mission'). ' 19', 'tmdisplay' => totranslate('Starting at mission'). ' 19', 'premium' => true],
      20 => ['name' => totranslate('Mission').' 20', 'description' => totranslate('Starting at mission'). ' 20', 'tmdisplay' => totranslate('Starting at mission'). ' 20', 'premium' => true],
      21 => ['name' => totranslate('Mission').' 21', 'description' => totranslate('Starting at mission'). ' 21', 'tmdisplay' => totranslate('Starting at mission'). ' 21', 'premium' => true],
      22 => ['name' => totranslate('Mission').' 22', 'description' => totranslate('Starting at mission'). ' 22', 'tmdisplay' => totranslate('Starting at mission'). ' 22', 'premium' => true],
      23 => ['name' => totranslate('Mission').' 23', 'description' => totranslate('Starting at mission'). ' 23', 'tmdisplay' => totranslate('Starting at mission'). ' 23', 'premium' => true],
      24 => ['name' => totranslate('Mission').' 24', 'description' => totranslate('Starting at mission'). ' 24', 'tmdisplay' => totranslate('Starting at mission'). ' 24', 'premium' => true],
      25 => ['name' => totranslate('Mission').' 25', 'description' => totranslate('Starting at mission'). ' 25', 'tmdisplay' => totranslate('Starting at mission'). ' 25', 'premium' => true],
      26 => ['name' => totranslate('Mission').' 26', 'description' => totranslate('Starting at mission'). ' 26', 'tmdisplay' => totranslate('Starting at mission'). ' 26', 'premium' => true],
      27 => ['name' => totranslate('Mission').' 27', 'description' => totranslate('Starting at mission'). ' 27', 'tmdisplay' => totranslate('Starting at mission'). ' 27', 'premium' => true],
      28 => ['name' => totranslate('Mission').' 28', 'description' => totranslate('Starting at mission'). ' 28', 'tmdisplay' => totranslate('Starting at mission'). ' 28', 'premium' => true],
      29 => ['name' => totranslate('Mission').' 29', 'description' => totranslate('Starting at mission'). ' 29', 'tmdisplay' => totranslate('Starting at mission'). ' 29', 'premium' => true],
      30 => ['name' => totranslate('Mission').' 30', 'description' => totranslate('Starting at mission'). ' 30', 'tmdisplay' => totranslate('Starting at mission'). ' 30', 'premium' => true],
      31 => ['name' => totranslate('Mission').' 31', 'description' => totranslate('Starting at mission'). ' 31', 'tmdisplay' => totranslate('Starting at mission'). ' 31', 'premium' => true],
      32 => ['name' => totranslate('Mission').' 32', 'description' => totranslate('Starting at mission'). ' 32', 'tmdisplay' => totranslate('Starting at mission'). ' 32', 'premium' => true],
      33 => ['name' => totranslate('Mission').' 33', 'description' => totranslate('Starting at mission'). ' 33', 'tmdisplay' => totranslate('Starting at mission'). ' 33', 'premium' => true],
      34 => ['name' => totranslate('Mission').' 34', 'description' => totranslate('Starting at mission'). ' 34', 'tmdisplay' => totranslate('Starting at mission'). ' 34', 'premium' => true],
      35 => ['name' => totranslate('Mission').' 35', 'description' => totranslate('Starting at mission'). ' 35', 'tmdisplay' => totranslate('Starting at mission'). ' 35', 'premium' => true],
      36 => ['name' => totranslate('Mission').' 36', 'description' => totranslate('Starting at mission'). ' 36', 'tmdisplay' => totranslate('Starting at mission'). ' 36', 'premium' => true],
      37 => ['name' => totranslate('Mission').' 37', 'description' => totranslate('Starting at mission'). ' 37', 'tmdisplay' => totranslate('Starting at mission'). ' 37', 'premium' => true],
      38 => ['name' => totranslate('Mission').' 38', 'description' => totranslate('Starting at mission'). ' 38', 'tmdisplay' => totranslate('Starting at mission'). ' 38', 'premium' => true],
      39 => ['name' => totranslate('Mission').' 39', 'description' => totranslate('Starting at mission'). ' 39', 'tmdisplay' => totranslate('Starting at mission'). ' 39', 'premium' => true],
      40 => ['name' => totranslate('Mission').' 40', 'description' => totranslate('Starting at mission'). ' 40', 'tmdisplay' => totranslate('Starting at mission'). ' 40', 'premium' => true],
      41 => ['name' => totranslate('Mission').' 41', 'description' => totranslate('Starting at mission'). ' 41', 'tmdisplay' => totranslate('Starting at mission'). ' 41', 'premium' => true],
      42 => ['name' => totranslate('Mission').' 42', 'description' => totranslate('Starting at mission'). ' 42', 'tmdisplay' => totranslate('Starting at mission'). ' 42', 'premium' => true],
      43 => ['name' => totranslate('Mission').' 43', 'description' => totranslate('Starting at mission'). ' 43', 'tmdisplay' => totranslate('Starting at mission'). ' 43', 'premium' => true],
      44 => ['name' => totranslate('Mission').' 44', 'description' => totranslate('Starting at mission'). ' 44', 'tmdisplay' => totranslate('Starting at mission'). ' 44', 'premium' => true],
      45 => ['name' => totranslate('Mission').' 45', 'description' => totranslate('Starting at mission'). ' 45', 'tmdisplay' => totranslate('Starting at mission'). ' 45', 'premium' => true],
      46 => ['name' => totranslate('Mission').' 46', 'description' => totranslate('Starting at mission'). ' 46', 'tmdisplay' => totranslate('Starting at mission'). ' 46', 'premium' => true],
      47 => ['name' => totranslate('Mission').' 47', 'description' => totranslate('Starting at mission'). ' 47', 'tmdisplay' => totranslate('Starting at mission'). ' 47', 'premium' => true],
      48 => ['name' => totranslate('Mission').' 48', 'description' => totranslate('Starting at mission'). ' 48', 'tmdisplay' => totranslate('Starting at mission'). ' 48', 'premium' => true],
      49 => ['name' => totranslate('Mission').' 49', 'description' => totranslate('Starting at mission'). ' 49', 'tmdisplay' => totranslate('Starting at mission'). ' 49', 'premium' => true],
      50 => ['name' => totranslate('Mission').' 50', 'description' => totranslate('Starting at mission'). ' 50', 'tmdisplay' => totranslate('Starting at mission'). ' 50', 'premium' => true],
    ]
  ],

  OPTION_CHALLENGE => [
    'name' => totranslate('Challenge mode for Three'),
    'values' => [
      CHALLENGE_OFF => [ 'name' => totranslate('Off')],
      CHALLENGE_ON => [ 'name' => totranslate('On')],
    ],
    'startcondition' => [
      CHALLENGE_ON => [
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
