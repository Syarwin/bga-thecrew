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
 * emptygame.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in emptygame_emptygame.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */

require_once( APP_BASE_PATH."view/common/game.view.php" );

class view_thecrewleocaseiro_thecrewleocaseiro extends game_view
{
  function getGameName() {
      return "thecrewleocaseiro";
  }
	function build_page( $viewArgs )
	{
	    // Get players & players number
      $players = $this->game->loadPlayersBasicInfos();
      $players_nbr = count( $players );

      /*********** Place your code below:  ************/

      $this->tpl['MISSION'] = self::_("Mission");
      $this->tpl['TRY'] = self::_("Mission attempts : ");
      $this->tpl['TOTALTRY'] = self::_("Total attempts : ");
      $this->tpl['TASKS'] = self::_("Available tasks");
      $this->tpl['NBR'] = $players_nbr;
      $this->tpl['YES'] = self::_("Yes");
      $this->tpl['NO'] = self::_("No");
      $this->tpl['WHATEVER'] = self::_("Whatever");
      $this->tpl['DISTRESS'] = self::_("Do you want to use the distress signal?");

      /*********** Do not change anything below this line  ************/
	}
}
