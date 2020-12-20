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


  public static function newHand($pId, $hand){
    self::notify($pId, 'newHand', clienttranslate('-- Your cards are:&nbsp;<br />${cards}'), [
      'cards' => self::listCardsForNotification($hand),
      'hand' => $hand,
    ]);
  }

  public static function newCommander($player){
    self::notifyAll('commander', clienttranslate('${player_name} is your new commander'), [
      'player_name' => $player->getName(),
      'player_id' => $player->getId(),
    ]);
  }


  public static function assignTask($task, $player){
    self::notifyAll('takeTask', clienttranslate('${player_name} takes task ${value_symbol}${color_symbol}'), [
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
      'player_name' => $player->getName(),
      'value' => $card['value'],
      'value_symbol' => $card['value'], // The substitution will be done in JS format_string_recursive function
      'color' => $card['color'],
      'color_symbol' => $card['color'], // The substitution will be done in JS format_string_recursive function
      'card' => $card,
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
