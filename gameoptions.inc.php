<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * EmptyGame implementation : © Nicolas Gocel <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * gameoptions.inc.php
 *
 * EmptyGame game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in emptygame.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

$game_options = array(

    100 => array(
        'name' => totranslate('Mission'),
        'values' => array(
            999 => array( 'name' => totranslate('Campaign'), 'tmdisplay' => totranslate('Continue existing campaign or start a new one') ),
            1 => array('name' => totranslate('Mission').' 1'),
            2 => array('name' => totranslate('Mission').' 2'),
            3 => array('name' => totranslate('Mission').' 3'),
            4 => array('name' => totranslate('Mission').' 4'),
            5 => array('name' => totranslate('Mission').' 5'),
            6 => array('name' => totranslate('Mission').' 6'),
            7 => array('name' => totranslate('Mission').' 7'),
            8 => array('name' => totranslate('Mission').' 8'),
            9 => array('name' => totranslate('Mission').' 9'),
            10 => array('name' => totranslate('Mission').' 10'),
            11 => array('name' => totranslate('Mission').' 11','premium' => true),
            12 => array('name' => totranslate('Mission').' 12','premium' => true),
            13 => array('name' => totranslate('Mission').' 13','premium' => true),
            14 => array('name' => totranslate('Mission').' 14','premium' => true),
            15 => array('name' => totranslate('Mission').' 15','premium' => true),
            16 => array('name' => totranslate('Mission').' 16','premium' => true),
            17 => array('name' => totranslate('Mission').' 17','premium' => true),
            18 => array('name' => totranslate('Mission').' 18','premium' => true),
            19 => array('name' => totranslate('Mission').' 19','premium' => true),
            20 => array('name' => totranslate('Mission').' 20','premium' => true),
            21 => array('name' => totranslate('Mission').' 21','premium' => true),
            22 => array('name' => totranslate('Mission').' 22','premium' => true),
            23 => array('name' => totranslate('Mission').' 23','premium' => true),
            24 => array('name' => totranslate('Mission').' 24','premium' => true),
            25 => array('name' => totranslate('Mission').' 25','premium' => true),
            26 => array('name' => totranslate('Mission').' 26','premium' => true),
            27 => array('name' => totranslate('Mission').' 27','premium' => true),
            28 => array('name' => totranslate('Mission').' 28','premium' => true),
            29 => array('name' => totranslate('Mission').' 29','premium' => true),
            30 => array('name' => totranslate('Mission').' 30','premium' => true),
            31 => array('name' => totranslate('Mission').' 31','premium' => true),
            32 => array('name' => totranslate('Mission').' 32','premium' => true),
            33 => array('name' => totranslate('Mission').' 33','premium' => true),
            34 => array('name' => totranslate('Mission').' 34','premium' => true),
            35 => array('name' => totranslate('Mission').' 35','premium' => true),
            36 => array('name' => totranslate('Mission').' 36','premium' => true),
            37 => array('name' => totranslate('Mission').' 37','premium' => true),
            38 => array('name' => totranslate('Mission').' 38','premium' => true),
            39 => array('name' => totranslate('Mission').' 39','premium' => true),
            40 => array('name' => totranslate('Mission').' 40','premium' => true),
            41 => array('name' => totranslate('Mission').' 41','premium' => true),
            42 => array('name' => totranslate('Mission').' 42','premium' => true),
            43 => array('name' => totranslate('Mission').' 43','premium' => true),
            44 => array('name' => totranslate('Mission').' 44','premium' => true),
            45 => array('name' => totranslate('Mission').' 45','premium' => true),
            46 => array('name' => totranslate('Mission').' 46','premium' => true),
            47 => array('name' => totranslate('Mission').' 47','premium' => true),
            48 => array('name' => totranslate('Mission').' 48','premium' => true),
            49 => array('name' => totranslate('Mission').' 49','premium' => true),
            50 => array('name' => totranslate('Mission').' 50','premium' => true),            
            )
        ),
/*    
    101 => array(
        'name' => totranslate('Challenge mode for Three'),
        'values' => array(
            1 => array( 'name' => totranslate('Off')),
            2 => array( 'name' => totranslate('On')),
        ),
        'startcondition' => array(
            2 => array(
                array(
                    'type' => 'maxplayers',
                    'value' => 3,
                    'message' => totranslate('Challenge mode for Three is only for 3 players.'),
                    'gamestartonly' => true
                ),
            )
        )
    ),   */
);


