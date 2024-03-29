<?php
namespace CREW\Game;

use thecrew;

class Notifications
{
  protected static function notifyAll($name, $msg, $data){

    self::updateArgs($data);
    thecrew::get()->notifyAllPlayers($name, $msg, $data);
  }

  protected static function notify($pId, $name, $msg, $data){
    self::updateArgs($data);
    thecrew::get()->notifyPlayer($pId, $name, $msg, $data);
  }


  public static function message($txt, $args = []){
    self::notifyAll('message', $txt, $args);
  }

  public static function messageTo($player, $txt, $args = []){
    $pId = ($player instanceof \CREW\Player)? $player->getId() : $player;
    self::notify($pId, 'message', $txt, $args);
  }

  public static function speak($player, $msg){
    self::notifyAll('speak', clienttranslate('${player_name} : ${msg}'), [
      'i18n' => ['msg'],
      'player' => $player,
      'msg' => $msg,
      'content' => $msg,
    ]);
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

  public static function newJarvisHand($hand)
  {
    self::notifyAll('newJarvisHand', clienttranslate('Jarvis cards are:&nbsp;<br />${cards}'), [
      'cards' => self::listCardsForNotification($hand),
      'hand' => $hand,
    ]);
  }

  public static function jarvisRevealNewCard($card, $column)
  {
    self::notifyAll('jarvisRevealNewCard', clienttranslate('Jarvis reveals ${cards}'), [
      'cards' => self::listCardsForNotification([$card]),
      'card' => $card,
      'column' => $column,
    ]);
  }

  public static function newCommander($player){
    self::notifyAll('commander', clienttranslate('${player_name} is your new commander'), [
      'player' => $player
    ]);
  }

  public static function clearReplies(){
    self::notifyAll('clearReplies', '', []);
  }

  public static function specialCrewMember($player, $crew){
    self::notifyAll('specialCrewMember', clienttranslate('${player_name} chooses ${special_name}'), [
      'player' => $player,
      'special_name' => $crew->getName(),
      'special_id' => $crew->getId(),
    ]);
  }


  public static function specialMemberMission46($crew){
    self::notifyAll('specialCrewMember', clienttranslate('${player_name} must win all pink cards'), [
      'player' => $crew,
      'special_id' => $crew->getId(),
    ]);
  }



  public static function specialMemberMission50($crew){
    self::notifyAll('specialCrewMember', clienttranslate('${player_name} must win the first four tricks'), [
      'player' => $crew,
      'special_id' => $crew->getId(),
    ]);
  }

  public static function special2MemberMission50($crew){
    self::notifyAll('specialCrewMember', clienttranslate('${player_name} must win the last trick'), [
      'player' => $crew,
      'special2_id' => $crew->getId(),
    ]);
  }



  public static function assignTask($task, $player){
    self::notifyAll('takeTask', clienttranslate('${player_name} takes task ${value_symbol}${color_symbol}'), [
      'player' => $player,
      'task' => $task,
    ]);
  }

  public static function playCard($card, $player){
    self::notifyAll('playCard', clienttranslate('${player_name} plays ${value_symbol}${color_symbol}'), [
      'player' => $player,
      'card' => $card,
    ]);
  }


  public static function winTrick($cards, $player){
    self::notifyAll('trickWin', clienttranslate('${player_name} wins the trick:&nbsp;<br />${cards}'), [
      'player' => $player,
      'cards' => self::listCardsForNotification($cards),
      'oCards' => $cards,
    ]);
  }


  public static function clearPreselect($player, $invalid = false){
    $msg = $invalid? clienttranslate("Your preselected card is not valid to play") : "";
    self::notify($player->getId(), "clearPreselect", $msg, []);
  }

  public static function preselect($player, $card){
    self::notify($player->getId(), "preselect", '', [
      'card' => $card,
    ]);
  }


  public static function newTrick(){
    self::notifyAll('newTrick', '', [
      'trickCount' => Globals::getTrickCount(),
      'players' => Players::getUiData(0),
    ]);
  }

  public static function updateTaskStatus($task, $player){
    $msg = $task['status'] == 'ok'?
        clienttranslate('${player_name} fulfilled task ${value_symbol}${color_symbol}')
      : clienttranslate('${player_name} failed task ${value_symbol}${color_symbol}');

    self::notifyAll('taskUpdate', $msg, [
      'player' => $player,
      'task' => $task,
    ]);
  }


  public static function swapCard($from, $to, $card, $column){
    $pIds = Players::getAll()->getIds();
    if ($from->getId() == JARVIS_ID) {
      $pId = array_values(array_diff($pIds, [JARVIS_ID, $to->getId()]))[0];
      self::notify($pId, 'jarvisLostCard', clienttranslate('Jarvis lost a random card'), []);
    } elseif ($to->getId() == JARVIS_ID) {
      self::notify($from->getId(), 'youLostCard', clienttranslate('You lost ${value_symbol}${color_symbol}'), [
        'player' => $to,
        'card' => $card,
        'column' => $column,
      ]);
    } else {
      self::notify($from->getId(), 'giveCard', clienttranslate('You lost ${value_symbol}${color_symbol}'), [
        'player' => $to,
        'card' => $card,
        'column' => $column,
      ]);
    }

    if ($to->getId() == JARVIS_ID) {
      $pId = array_values(array_diff($pIds, [JARVIS_ID, $from->getId()]))[0];
      self::notify($pId, 'receiveSwappedCardJarvis', clienttranslate('Jarvis picked a random card'), []);
    } else {
      self::notify($to->getId(), 'receiveCard', clienttranslate('You picked ${value_symbol}${color_symbol}'), [
        'card' => $card,
      ]);
    }
  }

  public static function swapTiles($task1, $task2){
    self::notifyAll('swapTiles', clienttranslate('Commander swapped the order of tasks:&nbsp;<br />${cards}'), [
      'cards' => self::listCardsForNotification([$task1, $task2]),
      'task1' => $task1,
      'task2' => $task2,
    ]);
  }

  /******************
   **** DISTRESS ****
   *****************/
  public static function chooseDirection($player, $dir){
    self::notifyAll('chooseDistressDirection', '', [
      'pId' => $player->getId(),
      'dir' => $dir,
    ]);
  }

  public static function chooseDistressDirection($dir){
    $msg = clienttranslate('No cards will be passed');
    if($dir == CLOCKWISE) $msg = clienttranslate('Cards will be turned clockwise');
    if($dir == ANTICLOCKWISE) $msg = clienttranslate('Cards will be turned anticlockwise');

    self::notifyAll('distressActivated', $msg, [
      'dir' => $dir
    ]);
  }

  public static function chooseDistressCard($player, $card){
    self::notify($player->getId(), 'chooseDistressCard', clienttranslate('You chose to give the ${value_symbol}${color_symbol}'), [
      'card' => $card,
    ]);
  }

  public static function chooseDistressCardJarvis($player, $card){
    self::notify($player->getId(), 'chooseDistressCard', clienttranslate('You chose Jarvis will give the ${value_symbol}${color_symbol}'), [
      'card' => $card,
    ]);
  }

  public static function distressExchange($from, $to, $card, $column){
    $pIds = Players::getAll()->getIds();
    if ($from->getId() == JARVIS_ID) {
      $pId = array_values(array_diff($pIds, [JARVIS_ID, $to->getId()]))[0];
      self::notify($pId, 'giveCard', clienttranslate('Jarvis gives ${value_symbol}${color_symbol} to ${player_name}'), [
        'card' => $card,
        'player' => $to,
      ]);
    } else {
      self::notify($from->getId(), 'giveCard', clienttranslate('You give ${value_symbol}${color_symbol} to ${player_name}'), [
        'card' => $card,
        'player' => $to,
        'column' => $column,
      ]);
    }

    if ($to->getId() == JARVIS_ID) {
      $pId = array_values(array_diff($pIds, [JARVIS_ID, $from->getId()]))[0];
      self::notify(
        $pId,
        'receiveCardJarvis',
        clienttranslate('Jarvis receive ${value_symbol}${color_symbol} from ${player_name}'),
        [
          'card' => $card,
          'player' => $from,
          'column' => $column,
        ]
      );
    } else {
      self::notify($to->getId(), 'receiveCard', clienttranslate('You receive ${value_symbol}${color_symbol} from ${player_name}'), [
        'card' => $card,
        'player' => $from,
      ]);
    }
  }



  /******************
   **** RESTART MISSION / FAIL MISSION ****
   *****************/
  public static function chooseRestartMission($player, $answer){
    if ($answer == DONT_WANT_FAIL_MISSION) {
      self::notifyAll('continue', clienttranslate('Mission will not fail, because ${player_name} wants to continue'), ['player' => $player]);
    } else {
      self::notifyAll('chooseRestartMission', clienttranslate('${player_name} chooses to fail mission'), [
        'player' => $player,
        'answer' => $answer
      ]);
    }
  }

  public static function restartMissionActivated($missionId){
    self::notifyAll('restartMissionActivated', clienttranslate('All players agreed to fail mission ${mission}'), [
      'mission' => $missionId,
    ]);
  }


  public static function continueMissions(){
    $player = Players::getCurrent();
    self::notifyAll('continue', clienttranslate('${player_name} wants to continue'), ['player' => $player]);
  }

  public static function stopMissions(){
    $player = Players::getCurrent();
    self::notifyAll('message', clienttranslate('${player_name} wants to stop'), ['player' => $player]);
  }

  public static function noPremium(){
    self::notifyAll('noPremium', clienttranslate('A premium member is required'), [] );
  }



  /***********************
   **** COMMUNICATION ****
   **********************/

  public static function toggleCommPending($player){
    self::notify($player->getId(), 'commpending', '', [
      'pending' => $player->isCommPending(),
      'canCommunicate' => $player->canCommunicate(),
    ]);
  }

  public static function startComm($player){
    self::notifyAll('startComm', clienttranslate('${player_name} starts communication'), ['player' => $player]);
  }

  public static function cancelComm($player){
    self::notifyAll('cancelComm', clienttranslate('${player_name} cancels communication'), ['player' => $player]);
  }

  public static function communicate($player, $card, $status){
    $msg = '';
    if($status == 'top')    $msg = clienttranslate('${player_name} tells ${value_symbol}${color_symbol} is their highest card of this color');
    if($status == 'middle') $msg = clienttranslate('${player_name} tells ${value_symbol}${color_symbol} is their only card of this color');
    if($status == 'bottom') $msg = clienttranslate('${player_name} tells ${value_symbol}${color_symbol} is their lowest card of this color');
    if($status == 'hidden') $msg = clienttranslate('${player_name} communicates ${value_symbol}${color_symbol}');

    self::notifyAll('endComm', $msg, [
      'player' => $player,
      'comm_status' => $status,
      'card' => $card,
    ]);
  }

  public static function usedComm($player){
    self::notifyAll('usedComm', '', [
      'player' => $player,
    ]);
  }




  /************************
   **** 5 PLAYERS RULE ****
   ***********************/
   public static function passGiveTask(){
     $player = Players::getCurrent();
     self::notifyAll('message', clienttranslate('${player_name} does not propose any exchange'), ['player' => $player]);
   }

   public static function proposeGiveTask($source, $task, $target){
     self::message(clienttranslate('${player_name} proposes to give task ${value_symbol}${color_symbol} to ${player_name2}'), [
       'player' => $source,
       'player_name2' => $target->getName(),
       'task' => $task,
     ]);
  }

  public static function confirmGiveTask(){
    $player = Players::getCurrent();
    self::notifyAll('confirmGiveTask', clienttranslate('${player_name} agrees with proposal'), ['player' => $player]);
  }

  public static function rejectGiveTask(){
    $player = Players::getCurrent();
    self::notifyAll('message', clienttranslate('${player_name} disagrees with proposal'), ['player' => $player]);
  }

  public static function giveTask($source, $task, $target){
    self::notifyAll('giveTask', clienttranslate('${player_name} gives task ${value_symbol}${color_symbol} to ${player_name2}'), [
      'player' => $source,
      'player_name2' => $target->getName(),
      'task' => $task,
    ]);
  }




  /*
   * Automatically adds some standard field about player and/or card/task
   */
  public static function updateArgs(&$args){
    if(isset($args['player'])){
      $args['player_name'] = $args['player']->getName();
      $args['player_id'] = $args['player']->getId();
      unset($args['player']);
    }
    if(isset($args['card']) || isset($args['task'])){
      $c = isset($args['card'])? $args['card'] : $args['task'];

      $args['value'] = $c['value'];
      $args['value_symbol'] = $c['value'];// The substitution will be done in JS format_string_recursive function
      $args['color'] = $c['color'];
      $args['color_symbol'] = $c['color'];// The substitution will be done in JS format_string_recursive function
    }
  }


  /*
   * Create the notification arguments for a list of cards
   */
  protected static function listCardsForNotification($cards) {
    // Grouping values by color
    $groupedValues = [];
    foreach($cards as $card) {
      // Ignore Jarvis hidden cards
      if (isset($card['color']) && isset($card['value'])) {
        if(!isset($groupedValues[ $card['color'] ]))
        $groupedValues[$card['color']] = [];

        $groupedValues[$card['color']][] = $card['value'];
      }
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
