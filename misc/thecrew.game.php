<?php
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
        $result['commander_id'] = self::getGameStateValue('commander_id');
        $result['special_id'] = self::getGameStateValue('special_id');
        $result['special2_id'] = self::getGameStateValue('special_id2');
        $result['colors'] = $this->colors;
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
    //////////// Player actions
    ////////////


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



    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    ////////////


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



    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////


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

}
