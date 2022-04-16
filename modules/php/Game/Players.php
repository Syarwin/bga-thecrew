<?php
namespace CREW\Game;
use CREW\JarvisPlayer;
use thecrewleocaseiro;
use CREW\Helpers\Collection;

/*
 * Players manager : allows to easily access players ...
 *  a player is an instance of Player class
 */
class Players extends \CREW\Helpers\DB_Manager
{
  protected static $table = 'player';
  protected static $primary = 'player_id';
  protected static $jarvis = ['id' => JARVIS_ID, 'no' => 99, 'name' => 'Jarvis', 'color' => 'black', 'nTricks' => 0];
  protected static function cast($row)
  {
    return new \CREW\Player($row);
  }


  public function setupNewGame($players)
  {
    // Create players
    self::DB()->delete();

    $gameInfos = thecrewleocaseiro::get()->getGameinfos();
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
    thecrewleocaseiro::get()->reattributeColorsBasedOnPreferences($players, $gameInfos['player_colors']);
    thecrewleocaseiro::get()->reloadPlayersBasicInfos();

    Globals::setPremium($atleastOnePremium);

    // setup Jarvis for 2 Players
    if (count($players) == 2) {
      GlobalsVars::setJarvis(true);
      GlobalsVars::setJarvisActive(false);
      GlobalsVars::setJarvisTricks(0);

      $ids = array_keys($players);
      $key = array_rand($ids);
      GlobalsVars::setJarvisPlaysAfter($ids[$key]);
    } else {
      GlobalsVars::setJarvis(false);
      GlobalsVars::setJarvisActive(false);
    }
  }

  public function getActiveId()
  {
    if (GlobalsVars::isJarvisActive()) {
      return JARVIS_ID;
    }
    return thecrewleocaseiro::get()->getActivePlayerId();
  }

  public function getCurrentId()
  {
    return thecrewleocaseiro::get()->getCurrentPId();
  }

  public function getAll(){
    $players = self::DB()->get(false);

    if (GlobalsVars::isJarvis()) {
      $players[JARVIS_ID] = self::getJarvis();
    }

    return $players;
  }

  public function getJarvis()
  {
    return new JarvisPlayer();
  }

  /*
   * get : returns the Player object for the given player ID
   */
  public function get($pId = null)
  {
    $pId = $pId ?: self::getActiveId();
    if ($pId == JARVIS_ID) {
      return self::getJarvis();
    }

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

  public function getNextId($player, $forceIncludeJarvis = false)
  {
    $pId = is_int($player) ? $player : $player->getId();
    if ($pId == GlobalsVars::getJarvisPlaysAfter() && (GlobalsVars::isJarvis() || $forceIncludeJarvis)) {
      return JARVIS_ID;
    } elseif ($pId == JARVIS_ID) {
      $pId = GlobalsVars::getJarvisPlaysAfter();
    }

    $table = thecrewleocaseiro::get()->getNextPlayerTable();
    return (int) $table[$pId];
  }

  public function getPrevId($player, $forceIncludeJarvis = false)
  {
    $pId = is_int($player) ? $player : $player->getId();
    if ($pId == JARVIS_ID) {
      return GlobalsVars::getJarvisPlaysAfter();
    }

    $table = thecrewleocaseiro::get()->getPrevPlayerTable();
    $pId = (int) $table[$pId];

    if ($pId == GlobalsVars::getJarvisPlaysAfter() && (GlobalsVars::isJarvis() || $forceIncludeJarvis)) {
      $pId = JARVIS_ID;
    }
    return $pId;
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
    return self::DB()->count() + (GlobalsVars::isJarvis() ? 1 : 0);
  }

  /*
   * getUiData : get all ui data of all players : id, no, name, team, color, powers list, farmers
   */
  public function getUiData($pId)
  {
    return self::getAll()->assocMap(function($player) use ($pId){ return $player->getUiData($pId); });
  }

  /*
   * Get current turn order according to first player variable
   */
  public function getTurnOrder($firstPlayer = null, $includeJarvis = false)
  {
    $firstPlayer = $firstPlayer ?? Globals::getCommander();
    $order = [];
    $p = $firstPlayer;
    do {
      $order[] = $p;
      $p = self::getNextId($p, $includeJarvis);
    } while ($p != $firstPlayer);
    return $order;
  }

  /**
   * This activate next player
   */
  public function activeNext($ignoreJarvis = false)
  {
    $pId = self::getActiveId();
    $nextPlayer = self::getNextId((int) $pId);
    if ($nextPlayer == JARVIS_ID && !$ignoreJarvis) {
      GlobalsVars::setJarvisActive(true);
      $nextPlayer = Globals::getCommander();
    } elseif ($nextPlayer == JARVIS_ID && $ignoreJarvis) {
      $nextPlayer = self::getNextId(JARVIS_ID);
    } else {
      GlobalsVars::setJarvisActive(false);
    }

    thecrewleocaseiro::get()->gamestate->changeActivePlayer($nextPlayer);
    return $nextPlayer;
  }

  /**
   * This allow to change next player taking into account Jarvis
   */
  public function changeActive($pId)
  {
    if (GlobalsVars::isJarvis()) {
      // We need to activate/desactivate Jarvis
      if ($pId == JARVIS_ID) {
        GlobalsVars::setJarvisActive(true);
        $pId = Globals::getCommander();
      } elseif (GlobalsVars::isJarvisActive()) {
        GlobalsVars::setJarvisActive(false);
      }
    }

    thecrewleocaseiro::get()->gamestate->changeActivePlayer($pId);
  }

  public function clearMission()
  {
    self::DB()->update([
      'distress_choice' => 0,
      'distress_card_id' => null,
      'comm_card_id' => null,
      'comm_token' => 'middle',
      'comm_pending' => 0,
      'player_trick_number' => 0,
      'reply_choice' => null,
      'preselect_card_id' => null,
    ])->run();
  }

  public function clearReplies()
  {
    self::DB()->update([
      'reply_choice' => null,
    ])->run();
  }

  public function clearDistressCards()
  {
    self::DB()->update([
      'distress_choice' => 0,
      'distress_card_id' => null,
    ])->run();
  }

  public function getNextToCommunicate()
  {
    return self::DB()->where('comm_pending', 1)->where('comm_token', '<>', 'used')->whereNull('comm_card_id')->limit(1)->get(true);
  }

  public function getAllDistressChoices() {
    return array_unique(self::getAll()->map(function($player){ return $player->getDistressChoice(); }));
  }

  public function getAllDistressChoicesAssoc() {
    return self::getAll()->assocMap(function($player){ return $player->getDistressChoice(); });
  }
}
