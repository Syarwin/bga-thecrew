<?php
namespace CREW\Game;
use thecrew;
use \CREW\Cards;

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
    'missionFinished' => 0,
    'distressDirection' => DONT_USE,
    'specialId' => 0,
    'specialId2' => 0,
    'checkCount' => 0,
    'endOfGame' => 0,

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

  public static function isPremium()
  {
    return self::get("premium");
  }

  public static function getCommander()
  {
    return (int) self::get("commanderId");
  }

  public static function getSpecial()
  {
    return (int) self::get('specialId');
  }

  public static function getSpecial2()
  {
    return (int) self::get('specialId2');
  }

  public static function getTrickCount()
  {
    return self::get("trickCount");
  }

  public static function getTrickColor()
  {
    return self::get("trickColor");
  }

  public static function getLastWinner()
  {
    return Players::get(self::get("lastWinnerId"));
  }

  public static function getMissionFinished()
  {
    return self::get("missionFinished");
  }

  public static function getDistressDirection()
  {
    return self::get("distressDirection");
  }

  public static function isEndOfGame()
  {
    return self::get("endOfGame") == 1;
  }

  public static function isLastTrick()
  {
    return Cards::countInHand() <= 1;
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
    self::set('specialId', 0);
    self::set('specialId2', 0);
  }

  public static function startNewTrick(){
    self::inc('trickCount');
    self::set('trickColor', 0);
  }

  public static function setTrickColor($color){
    self::set('trickColor', $color);
  }

  public static function setCommander($pId){
    self::set("commanderId", $pId);
    self::set('lastWinnerId', $pId);
  }

  public static function setLastWinner($winner){
    self::set('lastWinnerId', $winner->getId());
  }

  public static function setMissionFinished($value)
  {
    self::set('missionFinished', $value);
  }

  public static function setEndOfGame(){
    self::set('endOfGame', 1);
  }

  public static function setDistressDirection($dir){
    self::set('distressDirection', $dir);
  }

  public static function setSpecial($pId, $special = ''){
    self::set('specialId' . $special, $pId);
  }
}
