<?php
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
}
