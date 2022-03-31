<?php
namespace CREW\Game;
use thecrewleocaseiro;

class Stats
{
  protected static function init($type, $name, $value = 0){
    thecrewleocaseiro::get()->initStat($type, $name, $value);
  }

  public static function inc($name, $player = null, $value = 1, $log = true){
    $pId = is_null($player)? null : ( ($player instanceof \CREW\Player)? $player->getId() : $player );
    thecrewleocaseiro::get()->incStat($value, $name, $pId);
  }


  protected static function get($name, $player = null){
    thecrewleocaseiro::get()->getStat($name, $player);
  }

  protected static function set($value, $name, $player = null){
    $pId = is_null($player)? null : ( ($player instanceof \CREW\Player)? $player->getId() : $player );
    thecrewleocaseiro::get()->setStat($value, $name, $pId);
  }


  public static function setupNewGame(){
    $stats = thecrewleocaseiro::get()->getStatTypes();

//    self::init('table', 'turns_number');
    /*
    foreach ($stats['player'] as $key => $value) {
      if($value['id'] > 10 && $value['type'] == 'int' && $key != 'empty_slots_number')
        self::init('player', $key);
    }
    self::init('player', "empty_slots_number", 33);
    */
  }
}

?>
