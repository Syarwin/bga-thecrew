<?php
namespace CREW\Game;
use thecrew;

class Notifications
{
  protected static function notifyAll($name, $msg, $data){
    thecrew::get()->notifyAllPlayers($name, $msg, $data);
  }

  protected static function notify($pId, $name, $msg, $data){
    thecrew::get()->notifyPlayer($pId, $name, $msg, $data);
  }

  public static function message($txt, $args = []){
    self::notifyAll('message', $txt, $args);
  }

  public static function messageTo($player, $txt, $args = []){
    $pId = ($player instanceof \CREW\Player)? $player->getId() : $player;
    self::notify($pId, 'message', $txt, $args);
  }



}

?>
