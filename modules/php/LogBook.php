<?php
namespace CREW;
use CREW\Game\Globals;
use thecrew;

/*
 * Log: a class that allows to log some actions
 *   and then fetch these actions latter
 */
class LogBook extends \CREW\Helpers\DB_Manager
{
  protected static $table = 'logbook';
  protected static $primary = "mission";
  protected static $associative = false;
  protected static function cast($row)
  {
    return [
      'mission' => (int) $row['mission'],
      'attempt' => (int) $row['attempt'],
      'success' => (bool) ($row['success'] == 1),
      'distress' => (bool) ($row['distress'] == 1),
    ];
  }

  /*
   * Get status of current mission
   */
  public static function getStatus($mId = null)
  {
    $mId = $mId ?? Missions::getCurrentId();
    $data = self::DB()->get($mId);
    return [
      'mId' => $mId,
      'attempts' => $data['attempt'],
      'distress' => $data['distress'],
      'total' => (int) self::getUniqueValueFromDB("SELECT sum(attempt) FROM logbook"),
    ];
  }


  /*
   * Insert a new mission in the logbook
   */
  public static function insert($mission, $attempt = null, $success = null, $distress = null)
  {
    self::DB()->insert([
      'mission' => $mission,
      'attempt' => $attempt,
      'success' => $success,
      'distress' => $distress,
    ]);
  }


  /*
   * Start a new mission : insert in the logbook with default value
   */
  public static function startMission($mission)
  {
    self::insert($mission);
  }



  /*
   * Load a campaign : try to fetch legacy data for the team first
   */
  public static function loadCampaign()
  {
    if(!Globals::isCampaign())
      return;

    $json = thecrew::get()->retrieveLegacyTeamData();
    if(is_string($json)){
      $json = substr($json, 1, strlen($json)-2);
      $logs = json_decode ($json, true);
      foreach($logs as $log_id => $log){
        self::insert($log['mission'], $log['attempt'], $log['success'], $log['distress']);
      }
    }
    else {
      self::startMission(1);
    }
  }
}
