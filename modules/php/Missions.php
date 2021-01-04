<?php
namespace CREW;

/*
 * Missions: a class that handles missions
 */
class Missions extends \APP_DbObject
{
  public static function getUiData()
  {
    $ui = [];
    for($i = 1; $i <= 25; $i++){
      $className = "CREW\Missions\Mission".$i;
      $mission = new $className();
      $ui[] = $mission->getUiData();
    }
    return $ui;
  }


  public static function getCurrentId()
  {
    return self::getUniqueValueFromDB( "SELECT MAX(mission) FROM logbook");
  }

  public static function getCurrent()
  {
    $className = "CREW\Missions\Mission".self::getCurrentId();
    return new $className();
  }
}
