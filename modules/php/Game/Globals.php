<?php
namespace CREW\Game;
use thecrew;

/*
 * Globals
 */
class Globals extends \APP_DbObject
{
  /* Exposing methods from Table object singleton instance */
  protected static function init($name, $value){
    thecrew::get()->setGameStateInitialValue($name, $value);
  }

  protected static function set($name, $value){
    thecrew::get()->setGameStateValue($name, $value);
  }

  protected static function get($name, $value){
    return thecrew::get()->getGameStateValue($name, $value);
  }

  protected static function inc($name, $value){
    return thecrew::get()->incGameStateValue($name, $value);
  }


  /*
   * Declare globas (in the constructor of game.php)
   */
  private static $globals = [
    'trick_count' => 0,
    'commander_id' => 0,
    'last_winner' => 0,
    'trick_color' => 0,
    'mission_finished' => 0,
    'distress_turn' => 0,
    'special_id' => 0,
    'special_id2' => 0,
    'check_count' => 0,
    'end_game' => 0,
    'intro_shown' => 0,
    'premium' => false,
  ];

  public static function declare($game){
    // Game options label
    $labels = [
      "mission_start" => OPTION_MISSION,
      "challenge" => OPTION_CHALLENGE,
    ];

    // Add globals with indexes starting at 10
    $id = 10;
    foreach(self::$globals as $name => $initValue){
      $labels[$name] = $id++;
    }
    $game->initGameStateLabels($labels);
  }

  /*
   * Init
   */
  public static function setupNewGame(){
    foreach(self::$globals as $name => $initValue){
      self::init($name, $initValue);
    }
  }


  /*
   * Setters
   */
  public static function setPremium($premium){
    self::set('premium', $premium);
  }

/*
  public static function getCurrentTurn()
  {
    return (int) welcometo::get()->getGameStateValue('currentTurn');
  }
*/
}
