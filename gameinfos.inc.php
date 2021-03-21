<?php

$gameinfos = [
  'game_name' => clienttranslate("The Crew: The Quest for Planet Nine"),
  'designer' => 'Thomas Sing',
  'artist' => 'Marco Armbruster',
  'year' => 2019,
  'publisher' => 'KOSMOS',
  'publisher_website' => 'http://www.kosmos.de/',
  'publisher_bgg_id' => 37,
  'bgg_id' => 284083,
  'presentation' => [
    totranslate("In the co-operative trick-taking game The Crew: The Quest for Planet Nine, the players set out as astronauts on an uncertain space adventure."),
    totranslate("What about the rumors about the unknown planet about? The eventful journey through space extends over 50 exciting missions."),
    totranslate("But this game can only be defeated by meeting common individual tasks of each player. In order to meet the varied challenges, communication is essential in the team. But this is more difficult than expected in space."),
  ],


  'players' => [3,4,5],
  'suggest_player_number' => 4,
  'not_recommend_player_number' => null,

  'estimated_duration' => 20,
  'fast_additional_time' => 30,
  'medium_additional_time' => 40,
  'slow_additional_time' => 50,

  'tie_breaker_description' => "",

  'is_beta' => 1,
  'is_coop' => 1,

  'complexity' => 2,
  'luck' => 4,
  'strategy' => 5,
  'diplomacy' => 4,
  'tags' => [2, 10, 101, 200],

  'player_colors' => ["ff0000", "008000", "0000ff", "ffa500", "773300"],
  'favorite_colors_support' => true,

  'game_interface_width' => [
    'min' => 850,
    'max' => null
  ],



//////// BGA SANDBOX ONLY PARAMETERS (DO NOT MODIFY)
  'turnControl' => 'simple',
  'is_sandbox' => false,
////////
];
