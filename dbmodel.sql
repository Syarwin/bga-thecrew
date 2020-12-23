
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- The Crew implementation : © Nicolas Gocel <nicolas.gocel@gmail.com>
--
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- changements :
-- ALTER TABLE `player` ADD `comm_card_id` int(10) DEFAULT NULL COMMENT 'id of the communicated card';
-- renommer `card_id` en distress_card_id

-- renommer 'token' en 'tile'
-- renommer card_type en color
-- renommer card_type_arg en value

ALTER TABLE `player` ADD `player_trick_number` int(10) NOT NULL DEFAULT 0 COMMENT 'Number of tricks collected by the player during this hand';
ALTER TABLE `player` ADD `comm_token` varchar(16) NOT NULL DEFAULT 'middle' COMMENT 'communication token status: up, middle, bottom, used, hidden';
ALTER TABLE `player` ADD `comm_pending` smallint(1) NOT NULL DEFAULT '0' COMMENT 'want to communicate next time is possible';
ALTER TABLE `player` ADD `comm_card_id` int(10) DEFAULT NULL COMMENT 'id of the communicated card';
ALTER TABLE `player` ADD `distress_card_id` int(10) unsigned NULL COMMENT 'id of the card to pass with distress';

-- Standard schema to manage cards
--CREATE TABLE IF NOT EXISTS `card` (
--  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--  `card_type` varchar(16) NOT NULL COMMENT 'Color of the card: 1 => blue, 2 => green, 3 => pink, 4 => yellow, 5 => Rocket, 6 => reminder',
--  `card_type_arg` int(11) NOT NULL COMMENT 'Value of the card. Numeric value',
--  `card_location` varchar(16) NOT NULL COMMENT 'Deck, comm, hand, cardontable, trickX',
--  `card_location_arg` int(11) NOT NULL COMMENT 'The id of the owner if it means something',
--  PRIMARY KEY (`card_id`)
--) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- Standard schema to manage cards
CREATE TABLE IF NOT EXISTS `card` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_location` varchar(16) NOT NULL COMMENT 'Deck, comm, hand, cardontable, trickX',
  `card_state` int(11) NOT NULL,
  `color` int(4) NOT NULL COMMENT 'Color of the card: 1 => blue, 2 => green, 3 => pink, 4 => yellow, 5 => Rocket, 6 => reminder',
  `value` int(11) NOT NULL COMMENT 'Value of the card. Numeric value',
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



CREATE TABLE IF NOT EXISTS `task` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `color` varchar(16) NOT NULL COMMENT 'Color of the card: 1 => blue, 2 => green, 3 => pink, 4 => yellow, 5 => Rocket, 6 => reminder',
  `value` int(11) NOT NULL COMMENT 'Value of the card. Numeric value',
  `tile` varchar(3) NOT NULL DEFAULT '' COMMENT 'Tile on the task',
  `player_id` int(11) NULL COMMENT 'The id of the owner if it means something',
  `status` varchar(3) NOT NULL DEFAULT 'tbd' COMMENT 'tbd, nok, ok',
  `trick` int(5) NULL COMMENT 'trick number where task has been done',
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `logbook` (
  `mission` int(10) unsigned NOT NULL,
  `attempt` int(5) unsigned NOT NULL DEFAULT '1',
  `success` smallint(1) NOT NULL DEFAULT '0',
  `distress` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
