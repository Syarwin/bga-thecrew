<?php
namespace CREW;
use CREW\Game\Globals;
use CREW\Game\Players;
use CREW\Cards;
use CREW\Tasks;

class Player extends Helpers\DB_Manager
{
  protected static $table = 'player';
  protected static $primary = 'player_id';
  public function __construct($row)
  {
    $this->id = (int) $row['player_id'];
    $this->no = (int) $row['player_no'];
    $this->name = $row['player_name'];
    $this->color = $row['player_color'];
    $this->score = $row['player_score'];
    $this->eliminated = $row['player_eliminated'] == 1;
    $this->zombie = $row['player_zombie'] == 1;
    $this->nTricks = $row['player_trick_number'];
  }

  private $id;
  private $no; // natural order
  private $name;
  private $color;
  private $eliminated = false;
  private $zombie = false;
  private $score;
  private $nTricks;


  /////////////////////////////////
  /////////////////////////////////
  //////////   Getters   //////////
  /////////////////////////////////
  /////////////////////////////////

  public function getId(){ return $this->id; }
  public function getNo(){ return $this->no; }
  public function getName(){ return $this->name; }
  public function getColor(){ return $this->color; }
  public function isEliminated(){ return $this->eliminated; }
  public function isZombie(){ return $this->zombie; }

  public function getUiData($pId)
  {
    return [
      'id'        => $this->id,
      'no'        => $this->no,
      'name'      => $this->name,
      'color'     => $this->color,
      'score'     => $this->score,
      'nTricks'   => $this->nTricks,
      'cards'     => $pId == $this->id? $this->getCards() : [],
      'nCards'    => count($this->getCards()),
      'tasks'     => $this->getTasks(),
      'table'     => $this->getOnTable(),
    ];
  }

  public function getCards()
  {
    return Cards::getOfPlayer($this->id);
  }

  public function getTasks()
  {
    return Tasks::getOfPlayer($this->id);
  }

  public function getOnTable()
  {
    return Cards::getOnTable($this->id);
  }
  // TODO
  //            $sql = "update player set comm_token = 'used' where player_id=".$player_id;
  //            self::DbQuery( $sql );
}
