<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * thecrew implementation : © Nicolas Gocel <nicolas.gocel@gmail.com> & Timothée Pecatte <tim.pecatte@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * thecrew.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */

$swdNamespaceAutoload = function ($class)
{
  $classParts = explode('\\', $class);
  if ($classParts[0] == 'CREW') {
    array_shift($classParts);
    $file = dirname(__FILE__) . "/modules/php/" . implode(DIRECTORY_SEPARATOR, $classParts) . ".php";
    if (file_exists($file)) {
      require_once($file);
    } else {
      var_dump("Impossible to load thecrew class : $class");
    }
  }
};
spl_autoload_register($swdNamespaceAutoload, true, true);

require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');


class thecrew extends Table
{
  use CREW\States\MissionTrait;
  use CREW\States\PickTaskTrait;
  use CREW\States\TrickTrait;
  use CREW\States\CommunicationTrait;
  use CREW\States\DistressTrait;
  use CREW\States\QuestionTrait;
  use CREW\States\MoveTileTrait;


  public static $instance = null;
  public function __construct()
  {
    parent::__construct();
    self::$instance = $this;
    CREW\Game\Globals::declare($this);
  }

  public static function get()
  {
    return self::$instance;
  }

  protected function getGameName()
  {
      return "thecrew";
  }



  /*
   * setupNewGame:
   *  This method is called only once, when a new game is launched.
   * params:
   *  - array $players
   *  - mixed $options
   */
  protected function setupNewGame($players, $options = [])
  {
    CREW\Game\Globals::setupNewGame();
    CREW\Game\Players::setupNewGame($players);
    CREW\Game\Stats::setupNewGame();
    CREW\Cards::setupNewGame($players, $options);

    if($options[OPTION_MISSION] == CAMPAIGN)
      CREW\LogBook::loadCampaign();
    else
      CREW\LogBook::startMission($options[OPTION_MISSION]);


    $this->activeNextPlayer();
  }


  /*
   * getAllDatas:
   *  Gather all informations about current game situation (visible by the current player).
   *  The method is called each time the game interface is displayed to a player, ie: when the game starts and when a player refreshes the game page (F5)
   */
  protected function getAllDatas()
  {
    $pId = self::getCurrentPId();
    $status = CREW\LogBook::getStatus();
    return [
      'players' => CREW\Game\Players::getUiData($pId),
      'missions' => CREW\Missions::getUiData(),
      'status' => $status,
      'commanderId' => CREW\Game\Globals::getCommander(),
      'specialId' => CREW\Game\Globals::getSpecial(),
      'specialId2' => CREW\Game\Globals::getSpecial2(),
      'showIntro' => $status['mId'] == 1 && $status['total'] == 1 && CREW\Game\Globals::isCampaign(),
      'trickCount' => CREW\Game\Globals::getTrickCount(),
    ];
  }


  /*
   * getGameProgression:
   *  Compute and return the current game progression approximation
   *  This method is called each time we are in a game state with the "updateGameProgression" property set to true
   */
  public function getGameProgression()
  {
    $nbTotalCards = CREW\Game\Globals::isChallenge()? 30 : 40;
    $nbPlayedCards = $nbTotalCards - CREW\Cards::countRemeaning();

    return 100 * $nbPlayedCards / $nbTotalCards;
  }



  ////////////////////////////////////
  ////////////   Zombie   ////////////
  ////////////////////////////////////
  /*
   * zombieTurn:
   *   This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
   *   You can do whatever you want in order to make sure the turn of this player ends appropriately
   */
  public function zombieTurn($state, $activePlayer)
  {
    // Only one player active => try to zombiepass transition
    if ($state['type'] === "activeplayer") {
      // TODO
      /*
        self::setGameStateValue( 'mission_finished', -1 );
        self::setGameStateValue( 'end_game',1);
        $this->gamestate->nextState( "zombiePass" );
        break;
      */

      if (array_key_exists('zombiePass', $state['transitions'])) {
        $this->gamestate->nextState('zombiePass');
        return;
      }
    }
    // Multiactive => make player non-active
    else if ($state['type'] === "multipleactiveplayer") {
      $this->gamestate->setPlayerNonMultiactive($activePlayer, '');
      return;
    }

    throw new BgaVisibleSystemException('Zombie player ' . $activePlayer . ' stuck in unexpected state ' . $state['name']);
  }


  /////////////////////////////////////
  //////////   DB upgrade   ///////////
  /////////////////////////////////////
  // You don't have to care about this until your game has been published on BGA.
  // Once your game is on BGA, this method is called everytime the system detects a game running with your old Database scheme.
  // In this case, if you change your Database scheme, you just have to apply the needed changes in order to
  //   update the game database and allow the game to continue to run with your new version.
  /////////////////////////////////////
  /*
   * upgradeTableDb
   *  - int $from_version : current version of this game database, in numerical form.
   *      For example, if the game was running with a release of your game named "140430-1345", $from_version is equal to 1404301345
   */
  public function upgradeTableDb($from_version)
  {
    if( $from_version <= 2008261355 ){
      // ! important ! Use DBPREFIX_<table_name> for all tables
      $sql = "ALTER TABLE DBPREFIX_player ADD `comm_pending` smallint(1) NOT NULL DEFAULT '0'";
      self::applyDbUpgradeToAllDB( $sql );
    }
  }


  ///////////////////////////////////////////////////////////
  // Exposing proteced method, please use at your own risk //
  ///////////////////////////////////////////////////////////

  // Exposing protected method getCurrentPlayerId
  public static function getCurrentPId(){
    return self::getCurrentPlayerId();
  }

  // Exposing protected method translation
  public static function translate($text){
    return self::_($text);
  }
}
