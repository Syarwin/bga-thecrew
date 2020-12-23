<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * thecrew implementation : © Nicolas Gocel <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * thecrew.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */


require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');


class thecrew extends Table
{



    /*
     getAllDatas:

     Gather all informations about current game situation (visible by the current player).

     The method is called each time the game interface is displayed to a player, ie:
     _ when the game starts
     _ when a player refreshes the game page (F5)
     */
    protected function getAllDatas()
    {
        $result = array();
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!

        $relative = $this->getPlayerRelativePositions();

        $result['trick_count'] = self::getGameStateValue('trick_count');
        $result['mission'] = self::getUniqueValueFromDB( "SELECT max(mission) FROM logbook");
        $result['mission_attempts'] = self::getUniqueValueFromDB( "SELECT attempt FROM logbook where mission=".$result['mission']);// NOI18N;
        $result['distress'] = self::getUniqueValueFromDB( "SELECT distress FROM logbook where mission=".$result['mission']);// NOI18N;
        $result['total_attempts'] = self::getUniqueValueFromDB( "SELECT sum(attempt) FROM logbook");
        $result['commander_id'] = self::getGameStateValue('commander_id');
        $result['special_id'] = self::getGameStateValue('special_id');
        $result['special2_id'] = self::getGameStateValue('special_id2');
        $result['colors'] = $this->colors;

        // Cards in player hand
        $result['hand'] = $this->cards->getCardsInLocation('hand', $current_player_id);

        $sql = "SELECT player_id id, player_name, player_color, comm_pending, player_trick_number, comm_token , player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

        foreach($result['players'] as $player_id => $player) {
            $result['players'][$player_id]['relative'] = $relative[$player_id];
            $result['players'][$player_id]['cards_number'] = $this->cards->countCardInLocation('hand', $player_id);
            $cards = $this->cards->getCardsInLocation('cardsontable', $player_id);
            if(count($cards)>0)
            {
                $result['players'][$player_id]['cardontable'] = array_shift($cards);
            }
            $cards = $this->cards->getCardsInLocation('comm', $player_id);
            if(count($cards)>0)
            {
                $result['players'][$player_id]['comm'] = array_shift($cards);
            }


            $cards = $this->cards->getCardsInLocation('comm', $player_id);
            $notUsed = count($cards)==1 && array_shift($cards)['type'] == 6;
            $result['players'][$player_id]['canCommunicate'] = $player['comm_token'] != 'used' && $notUsed;
        }

        $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NOT NULL";
        $result['tasks'] = self::getCollectionFromDb( $sql );
        $result['show_intro'] = $result['mission'] == 1 && $result['total_attempts'] == 1 && $this->getGameStateValue("mission_start") == 999;
        return $result;
    }

    /*
     getGameProgression:

     Compute and return the current game progression.
     The number returned must be an integer beween 0 (=the game just started) and
     100 (= the game is finished or almost finished).

     This method is called each time we are in a game state with the "updateGameProgression" property set to true
     (see states.inc.php)
     */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        $trickCOunt =  $this->getGameStateValue( 'trick_count');
        $nbCards = $this->getGameStateValue( 'challenge') == 2?30:40;
        $nbPlayers = thecrew::getUniqueValueFromDB("SELECT count(*) from player");
        $nbTricks = intdiv($nbCards, $nbPlayers);

        $prog = 0;
        if($nbTricks>0)
        {
           $prog = $trickCOunt * 100 / $nbTricks;
        }
        return $prog;
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Utility functions
    ////////////

    function getLog()
    {
        $sql = "SELECT mission, attempt, success, distress FROM log";
        $log = self::getCollectionFromDb( $sql );
        return $log;
    }

    function getMission() {
        $missionnb = self::getUniqueValueFromDB( "SELECT max(mission) FROM logbook");
        return $this->missions[$missionnb];
    }

    function getPlayerName($player_id) {
        $players = self::loadPlayersBasicInfos();
        return $players[$player_id]['player_name'];
    }


    function getCommunicationCard($player_id)
    {
        $ret = null;
        $cards = $this->cards->getCardsInLocation('comm', $player_id);
        if(count($cards)>0)
        {
            $ret = array_shift($cards);
        }
        return $ret;
    }


    function getReminderCard($player_id)
    {
        $ret = null;
        $cards = $this->cards->getCardsOfTypeInLocation(6,0,'hand', $player_id);
        if(count($cards)>0)
        {
            $ret = array_shift($cards);
        }
        return $ret;
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    ////////////


    function actButton($button)
    {
        self::checkAction( 'actButton' );
        switch($this->gamestate->state()['name'])
        {
            case "distressSetup":
                $left = $button == 'left';
                $text = clienttranslate('Cards will be passed to the <b>left</b>');
                if($button == 'right'){
                    $text = clienttranslate('Cards will be passed to the <b>right</b>');
                }
                if($button == 'no'){
                    $text = clienttranslate('No cards will be passed');
                }



                self::notifyAllPlayers('note', $text ,array(
                'player_name' => self::getPlayerName(self::getCurrentPlayerId())
                ));

                if($button == 'no')
                {
                    $this->gamestate->nextState('turn');
                }
                else
                {
                    self::setGameStateValue( 'distress_turn', $left?1:0 );
                    $this->gamestate->setAllPlayersMultiactive();
                    $this->gamestate->nextState('next');
                }
                break;

            case "question":
                $index = intval($button);
                $mission = $this->getMission();
                $replies = $mission['replies'];
                $reply = explode("/", $replies)[$index];

                self::notifyAllPlayers('speak', '${player_name} : '.$reply ,array(// NOI18N
                    'player_id' => self::getCurrentPlayerId(),
                    'player_name' => self::getPlayerName(self::getCurrentPlayerId()),
                    'content' => $reply
                ));
                $this->gamestate->nextState('next');
                break;
        }
    }


    function actMultiSelect($id1, $id2) {
        self::checkAction("actMultiSelect");

        $mission = $this->getMission();

        switch($mission['id'])
        {
            case 23:
                $idl1 = str_replace ('marker_','', $id1);
                $idl2 = str_replace ('marker_','', $id2);
                $t1 =  self::getUniqueValueFromDB( "SELECT task_id FROM task where token = '".$idl1."'");
                $t2 =  self::getUniqueValueFromDB( "SELECT task_id FROM task where token = '".$idl2."'");

                $sql = "update task set token = '".$idl2."' where task_id=".$t1;
                self::DbQuery( $sql );
                $sql = "update task set token = '".$idl1."' where task_id=".$t2;
                self::DbQuery( $sql );

                $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where task_id=".$t1;
                $task1 = self::getObjectFromDb( $sql );

                $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where task_id=".$t2;
                $task2 = self::getObjectFromDb( $sql );

                self::notifyAllPlayers('move', '' ,array(
                    'player_id' => self::getCurrentPlayerId(),
                    'player_name' => self::getPlayerName(self::getCurrentPlayerId()),
                    'task' => $task2,
                    'item_id' => $id1,
                    'location_id' => 'task_'.$t2
                ));

                self::notifyAllPlayers('move', '' ,array(
                    'player_id' => self::getCurrentPlayerId(),
                    'player_name' => self::getPlayerName(self::getCurrentPlayerId()),
                    'task' => $task1,
                    'item_id' => $id2,
                    'location_id' => 'task_'.$t1
                ));

                $this->gamestate->nextState('task');
                break;

            case 40:
                $idl1 = str_replace ('marker_','', $id1);
                $idl2 = str_replace ('task_','', $id2);

                $sql = "update task set token = '' where token='".$idl1."'";
                self::DbQuery( $sql );

                $sql = "update task set token = '".$idl1."' where task_id=".$idl2;
                self::DbQuery( $sql );

                $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where task_id=".$idl2;
                $task2 = self::getObjectFromDb( $sql );

                self::notifyAllPlayers('move', '' ,array(
                    'player_id' => self::getCurrentPlayerId(),
                    'player_name' => self::getPlayerName(self::getCurrentPlayerId()),
                    'task' => $task2,
                    'item_id' => $id1,
                    'location_id' => 'task_'.$idl2
                ));
                $this->gamestate->nextState('task');

                break;
        }

    }


    function actDistress() {
       // self::checkAction("actDistress");
        if($this->gamestate->state()['name'] == 'playerTurn' && $this->cards->countCardInLocation('cardsontable')==0)
        {
            $mission = self::getUniqueValueFromDB( "SELECT max(mission) FROM logbook");

            $sql = "update logbook set distress = 1, attempt = attempt+1 where mission=".$mission;
            self::DbQuery( $sql );

            self::notifyAllPlayers('distress', clienttranslate('${player_name} launches a distress signal'),array(
                'player_name' => self::getPlayerName(self::getCurrentPlayerId())
            ));

            $this->gamestate->nextState('distress');
        }
    }


    function actPickCrew($crew_id)
    {
        self::checkAction("actPickCrew");

        $player_id = self::getActivePlayerId();
        $sql = "SELECT player_id, player_name FROM player where player_id = ".$player_id;
        $activePlayer = self::getObjectFromDB( $sql );

        $mission = $this->getMission();
        $distribution = array_key_exists('distribution', $mission);
        $down = array_key_exists('down', $mission);

        if(!$distribution && !$down)
        {
            if(self::getGameStateValue( 'special_id') == 0)
            {
                self::setGameStateValue( 'special_id', $crew_id );
                self::notifyAllPlayers('special', clienttranslate('${player_name} chooses ${special_name}'), array(
                    'player_id' => $crew_id,
                    'player_name' => $activePlayer['player_name'],
                    'special_name' => $this->getPlayerName($crew_id)
                ));
            }
            else
            {
                self::setGameStateValue( 'special_id2', $crew_id );
                self::notifyAllPlayers('special', clienttranslate('${player_name} chooses ${special_name} as second special crew'), array(
                    'player_id' => $crew_id,
                    'player_name' => $activePlayer['player_name'],
                    'special_name' => $this->getPlayerName($crew_id),
                    'special2' => true
                ));
            }
        }

        if($mission['id'] == 50)
        {
            if(self::getGameStateValue( 'special_id2') == 0)
            {
                $this->gamestate->nextState( 'pickCrew' );
                return;
            }
        }
        else if($mission['id'] == 11)
        {
            $sql = "update player set comm_token = 'used' where player_id=".$crew_id;
            self::DbQuery( $sql );

            $card = $this->getCommunicationCard($crew_id);

            self::notifyAllPlayers('endComm', '',array(
                'player_id' => $crew_id,
                'comm_status' => 'used',
                'card_id' => $card['id']
            ));
        }
        else if($down)
        {
            $sql = "update task set player_id =".$crew_id;
            self::DbQuery( $sql );

            $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NOT NULL";
            $tasks = self::getCollectionFromDb( $sql );

            foreach($tasks as $task_id => $task) {

                self::notifyAllPlayers('takeTask', clienttranslate('${player_name} takes task ${value_symbol}${color_symbol}'), array(
                    'task' => $task,
                    'player_id' => $crew_id,
                    'player_name' => $this->getPlayerName($crew_id),
                    'value' => $task['card_type_arg'],
                    'value_symbol' => $task['card_type_arg'], // The substitution will be done in JS format_string_recursive function
                    'color' => $task['card_type'],
                    'color_symbol' => $task['card_type'] // The substitution will be done in JS format_string_recursive function
                ));
            }
            $mission['tasks'] = 0;
        }
        else if($distribution)
        {
            $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NULL";
            $task = self::getObjectFromDb( $sql );

            $sql = "update task set player_id=".$crew_id." where task_id = ".$task['task_id'];
            self::DbQuery( $sql );

            $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where task_id = ".$task['task_id'];
            $task = self::getObjectFromDB( $sql );
            self::setGameStateValue( 'special_id', 0 );
            self::setGameStateValue( 'special_id2', 0 );

            self::notifyAllPlayers('takeTask', clienttranslate('${player_name} takes task ${value_symbol}${color_symbol}'), array(
                'task' => $task,
                'player_id' => $crew_id,
                'player_name' => $this->getPlayerName($crew_id),
                'value' => $task['card_type_arg'],
                'value_symbol' => $task['card_type_arg'], // The substitution will be done in JS format_string_recursive function
                'color' => $task['card_type'],
                'color_symbol' => $task['card_type'] // The substitution will be done in JS format_string_recursive function
            ));

            $nbTask = self::getUniqueValueFromDB( "SELECT count(*) FROM task");
            if($nbTask < $mission['tasks'])
            {
                $this->addOneTask();
                $this->gamestate->nextState( 'next' );
            }
            else
            {
                $this->gamestate->nextState( 'trick' );
            }
            return;

        }


        if($mission['tasks']>0)
        {
            $this->gamestate->nextState( 'task' );
        }
        else
        {
            $this->gamestate->nextState( 'trick' );
        }
    }




    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    ////////////

    function argQuestion()
    {
        $result = array();
        $mission = $this->getMission();
        $result['commander'] = $this->getPlayerName(self::getGameStateValue( 'commander_id'));
        $result['question'] = $mission['question'];
        $result['replies'] = $mission['replies'];
        $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NULL";
        $result['tasks'] = self::getCollectionFromDb( $sql );

        $mission = $this->getMission();
        $down = array_key_exists('down', $mission);
        if($down)
        {
            foreach($result['tasks'] as $task_id => $task) {
                $result['tasks'][$task_id]['card_type'] = 7;
                $result['tasks'][$task_id]['card_type_arg'] = 0;
            }
        }

        return $result;
    }

    function argPickCrew()
    {
        $result = array();
        $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NULL";
        $result['tasks'] = self::getCollectionFromDb( $sql );
        $mission = $this->getMission();
        $down = array_key_exists('down', $mission);
        if($down)
        {
            foreach($result['tasks'] as $task_id => $task) {
                $result['tasks'][$task_id]['card_type'] = 7;
                $result['tasks'][$task_id]['card_type_arg'] = 0;
            }
        }

        $evenly = 10;
        $evenlyLeft = 0;

        if(array_key_exists('distribution', $mission))
        {
            $nbPlayers = thecrew::getUniqueValueFromDB("SELECT count(*) from player");
            $evenly = intdiv($mission['tasks'], $nbPlayers);
            $evenlyLeft  = $mission['tasks'] % $nbPlayers;
            $playersAtMax  = thecrew::getUniqueValueFromDB("SELECT COUNT(*) FROM (SELECT count(player_id) from task where player_id is not null group by player_id having count(player_id)>".$evenly.") t");
            if($playersAtMax == NULL) $playersAtMax = 0;
        }
        $taskLeft =  $mission['tasks'] - thecrew::getUniqueValueFromDB("SELECT count(*) from task where player_id is not null");

        $result['possible'] = array();
        $sql = "SELECT player_id id, comm_token FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
        foreach($result['players'] as $player_id => $player) {

            $nbMissions = self::getUniqueValueFromDB( "SELECT count(*) FROM task where player_id=".$player_id);
            if($nbMissions < $evenly || ($nbMissions == $evenly && $evenlyLeft> $playersAtMax) )
            {
                if(($mission['id'] != 33 && $mission['id'] != 41 && $mission['id'] != 20) || $player_id != self::getGameStateValue('commander_id'))
                {
                    $result['possible'][$player_id] = $player_id;
                }
            }
        }
        return $result;
    }

    function argDistress()
    {
        $result = array();

        $sql = "SELECT card_id id FROM card where card_type <5";
        $result['cards'] = self::getCollectionFromDb( $sql );

        return $result;
    }

    function argMultiSelect(){

        $result = array();
        $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where player_id IS NULL";
        $result['tasks'] = self::getCollectionFromDb( $sql );
        $result['ids'] = array();

        $mission = $this->getMission();

        switch($mission['id'])
        {
            case 23:
            for($i=1;$i<=5;$i++)
            {
                $result['ids']['marker_'.$i] = array();
                for($j=1;$j<=5;$j++)
                {
                    if($i != $j)
                    {
                        $result['ids']['marker_'.$i]['marker_'.$j] = 'marker_'.$j;
                    }
                }
            }
            break;

            case 40:
                $sql = "SELECT task_id, card_type, card_type_arg, token, player_id, status FROM task where token = ''";
                $tasks = self::getCollectionFromDb( $sql );

                for($i=1;$i<=3;$i++)
                {
                    $result['ids']['marker_'.$i] = array();
                    foreach($tasks as $task_id => $task)
                    {
                        $result['ids']['marker_'.$i]['task_'.$task_id] = 'task_'.$task_id;
                    }
                }

            break;
        }

        return $result;
    }


    function argPlayerTurn()
    {
        $result = array();
        $result['cards'] = array();
        $player_id = self::getActivePlayerId();

        $comCard = $this->getCommunicationCard($player_id);
        $reminderCard = $this->getReminderCard($player_id);
        $current_trick_color = self::getGameStateValue('trick_color');
        $hand = array();
        if($current_trick_color != 0)
        {
            $hand = $this->cards->getCardsOfTypeInLocation($current_trick_color, null, 'hand', $player_id);
            if($comCard['type'] == $current_trick_color)
            {
                $hand[$comCard['id']] = $comCard;
                $hand[$reminderCard['id']] = $reminderCard;
            }
        }
        if(count($hand)==0)
        {
            $hand = $this->cards->getCardsInLocation('hand', $player_id);
            if($comCard['type'] != COMM)
            {
                $hand[$comCard['id']] = $comCard;
            }
        }

        foreach($hand as $card_id => $card) {
            $result['cards'][] = $card_id;
        }

        $mission = $this->getMission();
        $disruption = array_key_exists('disruption', $mission) && $mission['disruption'] > self::getGameStateValue( 'trick_count');

        $sql = "SELECT player_id id, comm_token FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

        $mission = self::getUniqueValueFromDB( "SELECT max(mission) FROM logbook");
        $alreadyDistress = self::getUniqueValueFromDB( "SELECT distress FROM logbook where mission=".$mission);// NOI18N;
        $cardPlayed = $this->cards->countCardInLocation('cardsontable');

        $noComm = true;
        foreach($result['players'] as $player_id => $player) {
            $cards = $this->cards->getCardsInLocation('comm', $player_id);
            $notUsed = count($cards)==1 && array_shift($cards)['type'] == 6;

            if(!$notUsed)
            {
                $noComm = false;
            }

            $result['players'][$player_id]['canCommunicate'] = !$disruption && $current_trick_color == 0 && $player['comm_token'] != 'used' && $notUsed;
        }

        $result['canDistress'] = $alreadyDistress == 0 && $cardPlayed == 0 && $noComm && self::getGameStateValue( 'trick_count') ==1;

        return $result;
    }




    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////


    function stNextQuestion()
    {
        $this->activeNextPlayer();
        if($this->getActivePlayerId() == self::getGameStateValue('commander_id'))
        {
            $this->gamestate->nextState( 'pick' );
        }
        else
        {
            $this->gamestate->nextState( 'next' );
        }
    }


    function stBeforeComm()
    {
        $sql = "SELECT player_id, comm_token FROM player where comm_pending = 1 and comm_token <> 'used' limit 1";
        $player_id = self::getUniqueValueFromDb( $sql );

        $mission = $this->getMission();
        $disruption = array_key_exists('disruption', $mission) && $mission['disruption'] > self::getGameStateValue( 'trick_count');

        if(!$disruption && $player_id != NULL)
        {
            self::notifyAllPlayers('note', clienttranslate('${player_name} starts communication'),array(
                'player_name' => self::getPlayerName($player_id)
            ));
            $this->gamestate->changeActivePlayer($player_id);
            $this->gamestate->nextState('comm');
        }
        else
        {
            $this->gamestate->changeActivePlayer(self::getGameStateValue('last_winner'));
            $this->gamestate->nextState('turn');
        }
    }


    function swapOneCard()
    {
        $sql = "SELECT player_id id, player_no, card_id FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

        foreach($result['players'] as $player_id => $player) {
            $cards = $this->cards->getCardsInLocation('hand', $player_id);
            $index = bga_rand(0, count($cards)-1);
            $card = array_values($cards)[$index];

            while($card['type'] == COMM)
            {
                $index = bga_rand(0, count($cards)-1);
                $card = array_values($cards)[$index];
            }

            $result['players'][$player_id]['card'] = $card;
        }

        foreach($result['players'] as $player_id => $player) {

            $rel = $this->getPlayerRelativePositions($player_id);
            $next = $this->getPlayerAfter($player_id);

            $cardGiven = $result['players'][$player_id]['card'];
            $cardReceive = $result['players'][$this->getPlayerBefore($player_id)]['card'];

            $card_id = $player['card_id'];
            $this->cards->moveCard($cardGiven['id'], 'hand', $next);

            self::notifyPlayer($player_id, 'give', clienttranslate('You lost ${value_symbol}${color_symbol}'),array(
                'card_id' => $cardGiven['id'],
                'card' => $cardGiven,
                'value' => $cardGiven['type_arg'],
                'value_symbol' => $cardGiven['type_arg'], // The substitution will be done in JS format_string_recursive function
                'color' => $cardGiven['type'],
                'color_symbol' => $cardGiven['type'] // The substitution will be done in JS format_string_recursive function
            ));

            self::notifyPlayer($next, 'receive', clienttranslate('You picked ${value_symbol}${color_symbol}'),array(
                'card_id' => $cardGiven['id'],
                'card' => $cardGiven,
                'value' => $cardGiven['type_arg'],
                'value_symbol' => $cardGiven['type_arg'], // The substitution will be done in JS format_string_recursive function
                'color' => $cardGiven['type'],
                'color_symbol' => $cardGiven['type'] // The substitution will be done in JS format_string_recursive function
            ));
        }
    }

    function stDistressExchange()
    {
        $sql = "SELECT player_id id, player_no, card_id FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

        foreach($result['players'] as $player_id => $player) {

            $rel = $this->getPlayerRelativePositions($player_id);
            $next = $this->getPlayerAfter($player_id);
            if(self::getGameStateValue('distress_turn') == 0)
            {
                $next = $this->getPlayerBefore($player_id);
            }

            $card_id = $player['card_id'];
            $this->cards->moveCard($card_id, 'hand', $next);


            self::notifyPlayer($player_id, 'give', '', array(
                'card_id' => $card_id,
            ));

            $card = $this->cards->getCard($card_id);
            self::notifyPlayer($next, 'receive', clienttranslate('You receive ${value_symbol}${color_symbol}'),array(
                'card_id' => $card['id'],
                'card' => $card,
                'value' => $card['type_arg'],
                'value_symbol' => $card['type_arg'], // The substitution will be done in JS format_string_recursive function
                'color' => $card['type'],
                'color_symbol' => $card['type'] // The substitution will be done in JS format_string_recursive function
            ));
        }

        $this->gamestate->changeActivePlayer(self::getGameStateValue('commander_id'));
        $this->gamestate->nextState('next');
    }



    function stSave()
    {
        if($this->getGameStateValue("mission_start") == 999)
        {
            $sql = "SELECT mission, attempt, success, distress FROM logbook ";
            $logs = self::getCollectionFromDb( $sql );
            $json = json_encode ($logs);
            $this->storeLegacyTeamData($json);
        }
        $this->gamestate->nextState('next');
    }
}
