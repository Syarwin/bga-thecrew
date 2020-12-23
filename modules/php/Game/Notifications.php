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

  public static function cleanUp(){
    self::notifyAll('cleanUp', '', [
      'status' => \CREW\LogBook::getStatus(),
      'players' => Players::getUiData(0),
    ]);
  }


  public static function newHand($pId, $hand){
    self::notify($pId, 'newHand', clienttranslate('-- Your cards are:&nbsp;<br />${cards}'), [
      'cards' => self::listCardsForNotification($hand),
      'hand' => $hand,
    ]);
  }

  public static function newCommander($player){
    self::notifyAll('commander', clienttranslate('${player_name} is your new commander'), [
      'player_id' => $player->getId(),
      'player_name' => $player->getName(),
    ]);
  }


  public static function assignTask($task, $player){
    self::notifyAll('takeTask', clienttranslate('${player_name} takes task ${value_symbol}${color_symbol}'), [
      'player_id' => $player->getId(),
      'player_name' => $player->getName(),
      'value' => $task['value'],
      'value_symbol' => $task['value'], // The substitution will be done in JS format_string_recursive function
      'color' => $task['color'],
      'color_symbol' => $task['color'], // The substitution will be done in JS format_string_recursive function
      'task' => $task,
    ]);
  }

  public static function playCard($card, $player){
    self::notifyAll('playCard', clienttranslate('${player_name} plays ${value_symbol}${color_symbol}'), [
      'player_id' => $player->getId(),
      'player_name' => $player->getName(),
      'value' => $card['value'],
      'value_symbol' => $card['value'], // The substitution will be done in JS format_string_recursive function
      'color' => $card['color'],
      'color_symbol' => $card['color'], // The substitution will be done in JS format_string_recursive function
      'card' => $card,
      'pId' => $player->getId(),
    ]);
  }


  public static function winTrick($cards, $player){
    self::notifyAll('trickWin', clienttranslate('${player_name} wins the trick:&nbsp;<br />${cards}'), [
      'player_id' => $player->getId(),
      'player_name' => $player->getName(),
      'cards' => self::listCardsForNotification($cards),
      'oCards' => $cards,
    ]);
  }


  public static function updateTaskStatus($task, $player){
    $msg = $task['status'] == 'ok'?
        clienttranslate('${player_name} fulfilled task ${value_symbol}${color_symbol}')
      : clienttranslate('${player_name} failed task ${value_symbol}${color_symbol}');

    self::notifyAll('taskUpdate', $msg, [
      'player_id' => $player->getId(),
      'player_name' => $player->getName(),
      'value' => $task['value'],
      'value_symbol' => $task['value'], // The substitution will be done in JS format_string_recursive function
      'color' => $task['color'],
      'color_symbol' => $task['color'], // The substitution will be done in JS format_string_recursive function
      'task' => $task,
    ]);
  }


  public static function continueMissions(){
    $player = Players::getCurrent();
    self::notifyAll('continue', clienttranslate('${player_name} wants to continue'), $player->getForNotif());
  }

  public static function stopMissions(){
    $player = Players::getCurrent();
    self::notifyAll('message', clienttranslate('${player_name} wants to stop'), $player->getForNotif());
  }

  public static function noPremium(){
    self::notifyAll('noPremium', clienttranslate('A premium member is required'), [] );
  }


  public static function toggleCommPending($player){
    self::notify($player->getId(), 'commpending', '', [
      'pending' => $player->isCommPending(),
      'canCommunicate' => $player->canCommunicate(),
    ]);
  }

  public static function startComm($player){
    self::notifyAll('startComm', clienttranslate('${player_name} starts communication'), $player->getForNotif());
  }

  public static function cancelComm($player){
    self::notifyAll('cancelComm', clienttranslate('${player_name} cancels communication'), $player->getForNotif());
  }

  public static function communicate($player, $card, $status){
    $msg = '';
    if($status == 'top')    $msg = clienttranslate('${player_name} tells ${value_symbol}${color_symbol} is their highest card of this color');
    if($status == 'middle') $msg = clienttranslate('${player_name} tells ${value_symbol}${color_symbol} is their only card of this color');
    if($status == 'bottom') $msg = clienttranslate('${player_name} tells ${value_symbol}${color_symbol} is their lowest card of this color');

    self::notifyAll('endComm', $msg, [
      'player_name' => $player->getName(),
      'player_id' => $player->getId(),
      'comm_status' => $status,
      'card' => $card,
      'value' => $card['value'],
      'value_symbol' => $card['value'], // The substitution will be done in JS format_string_recursive function
      'color' => $card['color'],
      'color_symbol' => $card['color'] // The substitution will be done in JS format_string_recursive function
    ]);
  }

  public static function usedComm($player){
    self::notifyAll('usedComm', '', [
      'pId' => $player->getId(),
    ]);
  }


  // Create the notification arguments
  protected static function listCardsForNotification($cards) {
    // Grouping values by color
    $groupedValues = [];
    foreach($cards as $card) {
      if(!isset($groupedValues[ $card['color'] ]))
        $groupedValues[$card['color']] = [];

      $groupedValues[$card['color']][] = $card['value'];
    }
    ksort($groupedValues);


    // Foreach color, list the values
    $args = [];
    $colorKeys = [];
    foreach($groupedValues as $color => $values) {
      sort($values);
      $colorKey = 'color_'.$color;
      $colorKeys[] = '${'.$colorKey.'}';

      $valueKeys = [];
      $valuesArgs = [];
      $i = 1;
      foreach($values as $value) {
        $valueKey = 'card_'.$color.'_'.$value;
        $valueKeys[] = '${'.$valueKey.'}';
        $valuesArgs[$valueKey] = [
          'log' => ($i == count($values) ? '${value_symbol} ${color_symbol}' : '${value_symbol}'),
          'args' => [
            'value_symbol' => $value,
            'color_symbol' => $color
          ]
        ];
        $i++;
      }

      $valuesLog = join(' ', $valueKeys);
      $args[$colorKey] = [
        'log' => $valuesLog,
        'args' => $valuesArgs
      ];
    }

    return [
      'log' => join('&nbsp;<br />', $colorKeys),
      'args' => $args
    ];
  }
}

?>
