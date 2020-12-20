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

  public static function get($name){
    return thecrew::get()->getGameStateValue($name);
  }

  protected static function inc($name, $value = 1){
    return thecrew::get()->incGameStateValue($name, $value);
  }


  /*
   * Declare globas (in the constructor of game.php)
   */
  private static $globals = [
    'trickCount' => 0,
    'commanderId' => 0,
    'lastWinnerId' => 0,
    'trickColor' => 0,

    'mission_finished' => 0,
    'distress_turn' => 0,
    'special_id' => 0,
    'special_id2' => 0,
    'checkCount' => 0,
    'end_game' => 0,
    'intro_shown' => 0,
    'premium' => false,
  ];

  public static function declare($game){
    // Game options label
    $labels = [
      "startingMission" => OPTION_MISSION,
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
   * Getters
   */
  public static function isCampaign()
  {
    return self::get("startingMission") == CAMPAIGN;
  }

  public static function isChallenge()
  {
    return self::get("challenge") == CHALLENGE_ON;
  }

  public static function getCommander()
  {
    return self::get("commanderId");
  }

  public static function getTrickCount()
  {
    return self::get("trickCount");
  }

  public static function getTrickColor()
  {
    return self::get("trickColor");
  }


  /*
   * Setters
   */
  public static function setPremium($premium){
    self::set('premium', $premium);
  }

  public static function startNewMission(){
    self::set('trickCount', 0);
    self::set('checkCount', 0);

    // TODO ???
    self::set( 'special_id', 0);
    self::set( 'special_id2', 0);
  }

  public static function startNewTrick(){
    self::inc('trickCount');
    self::set('trickColor', 0);

    // TODO ???
    self::set( 'special_id', 0);
    self::set( 'special_id2', 0);
  }

  public static function setTrickColor($color){
    self::set('trickColor', $color);
  }

  public static function setCommander($pId){
    self::set("commanderId", $pId);
    self::set('lastWinnerId', $pId);
  }
}
