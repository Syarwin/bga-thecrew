<?php
namespace CREW\Game;
use thecrew;

/*
 * Players manager : allows to easily access players ...
 *  a player is an instance of Player class
 */
class Players extends \CREW\Helpers\DB_Manager
{
  protected static $table = 'player';
  protected static $primary = 'player_id';
  protected static function cast($row)
  {
    return new \CREW\Player($row);
  }


  public function setupNewGame($players)
  {
    // Create players
    self::DB()->delete();

    $gameInfos = thecrew::get()->getGameinfos();
    $colors = $gameInfos['player_colors'];
    $query = self::DB()->multipleInsert(['player_id', 'player_color', 'player_canal', 'player_name', 'player_avatar', 'player_score']);
    $values = [];
    $atleastOnePremium = false;
    foreach ($players as $pId => $player) {
      $color = array_shift($colors);
      $values[] = [ $pId, $color, $player['player_canal'], $player['player_name'], $player['player_avatar'], 1];
      $atleastOnePremium = $atleastOnePremium || $player['player_is_premium'];
    }
    $query->values($values);
    thecrew::get()->reattributeColorsBasedOnPreferences($players, $gameInfos['player_colors']);
    thecrew::get()->reloadPlayersBasicInfos();

    Globals::setPremium($atleastOnePremium);
  }



  public function getActiveId()
  {
    return thecrew::get()->getActivePlayerId();
  }

  public function getCurrentId()
  {
    return thecrew::get()->getCurrentPId();
  }

  public function getAll(){
    return self::DB()->get(false);
  }

  /*
   * get : returns the Player object for the given player ID
   */
  public function get($pId = null)
  {
    $pId = $pId ?: self::getActiveId();
    return self::DB()->where($pId)->get();
  }

  public function getActive()
  {
    return self::get();
  }

  public function getCurrent()
  {
    return self::get(self::getCurrentId());
  }

  public function getCommander()
  {
    return self::get(Globals::getCommander());
  }

  public function getNextId($player)
  {
    $table = thecrew::get()->getNextPlayerTable();
    return $table[$player->getId()];
  }

  public function alreadyCommmunicate()
  {
    return self::DB()->where('comm_card_id', '>', 0)->count() > 0;
  }

  /*
   * Return the number of players
   */
  public function count()
  {
    return self::DB()->count();
  }


  /*
   * getUiData : get all ui data of all players : id, no, name, team, color, powers list, farmers
   */
  public function getUiData($pId)
  {
    return self::getAll()->assocMap(function($player) use ($pId){ return $player->getUiData($pId); });
  }


  public function clearMission()
  {
    self::DB()->update([
      'distress_card_id' => 'NULL',
      'comm_card_id' => 'NULL',
      'comm_token' => 'middle',
      'comm_pending' => 0,
      'player_trick_number' => 0,
    ])->run();
  }
}
