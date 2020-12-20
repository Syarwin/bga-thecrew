<?php
namespace CREW\Missions;

class Mission46 extends AbstractMission
{
  public function __construct(){
    $this->id = 46;
//    $this->desc = clienttranslate('Most of the modules are still on emergency back-up supply while you puzzle over the cause of the rapid cooling. Callisto, one of the 79 moons of Jupiter, passes by, the moment you appear to escape the frost field. <b>Before you select the task cards, you can swap the position of two task tokens. Decide together but do not reveal anything about your own cards.</b>');
//    $this->tasks = 5;
//    $this->tiles = [1,2,3,4,5];

    //$this->questions =  'question' => clienttranslate('Do you want to take the task?'), 'replies' => clienttranslate('Yes/No')
  }

  public function getStartingState()
  {
    return 'trick';
  }

/*
$card_id = self::getUniqueValueFromDB( "SELECT card_id FROM card where card_type = '3' and card_type_arg = 9");
$card = $this->cards->getCard($card_id);

// And notify
self::notifyAllPlayers('note', clienttranslate('${player_name} has ${value_symbol}${color_symbol}'), array(
    'card_id' => $card_id,
    'card' => $card,
    'player_id' => $card['location_arg'],
    'player_name' => self::getPlayerName($card['location_arg']),
    'value' => $card['type_arg'],
    'value_symbol' => $card['type_arg'], // The substitution will be done in JS format_string_recursive function
    'color' => $card['type'],
    'color_symbol' => $card['type'], // The substitution will be done in JS format_string_recursive function
    ));

$crew_id = $this->getPlayerAfter($card['location_arg']);

self::setGameStateValue( 'special_id', $crew_id );

self::notifyAllPlayers('special', clienttranslate('${player_name} must win all pink cards'), array(
    'player_id' => $crew_id,
    'player_name' => $this->getPlayerName($crew_id)
));
$this->gamestate->nextState( 'trick' );
*/
}
