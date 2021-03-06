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
    $this->score = (int) $row['player_score'];
    $this->eliminated = $row['player_eliminated'] == 1;
    $this->zombie = $row['player_zombie'] == 1;
    $this->nTricks = $row['player_trick_number'];
    $this->commCard = $row['comm_card_id'];
    $this->commToken = $row['comm_token'];
    $this->commPending = (int) $row['comm_pending'];
    $this->distressChoice = $row['distress_choice'];
    $this->distressCard = $row['distress_card_id'];
    $this->reply = $row['reply_choice'];
  }

  private $id;
  private $no; // natural order
  private $name;
  private $color;
  private $eliminated = false;
  private $zombie = false;
  private $score;
  private $nTricks;
  private $commCard;
  private $commToken;
  private $commPending;
  private $distressChoice;
  private $distressCard;


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
  public function getDistressChoice(){ return $this->distressChoice; }
  public function getDistressCard(){ return Cards::get($this->distressCard); }
  public function getTricksWon(){ return $this->nTricks; }
  public function isCommander() { return $this->id == Globals::getCommander(); }
  public function isSpecial() { return $this->id == Globals::getSpecial(); }

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
      'commToken' => $this->commToken,
      'commCard'  => $this->getCardOnComm(),
      'commPending' => $this->isCommPending(),
      'canCommunicate' => $this->canCommunicate(),
      'distressChoice' => $this->distressChoice,
      'distressCard' => $pId == $this->id? $this->distressCard : null,
      'reply' => $this->reply,
    ];
  }

  public function getCards()
  {
    return Cards::getOfPlayer($this->id);
  }

  public function getRandomCard()
  {
    $cards = $this->getCards();
    $index = array_rand($cards, 1);
    return $cards[$index];
  }

  public function getTasks()
  {
    return Tasks::getOfPlayer($this->id);
  }

  public function countTasks()
  {
    return count($this->getTasks());
  }

  public function getOnTable()
  {
    return Cards::getOnTable($this->id);
  }

  public function getCardOnComm()
  {
    return is_null($this->commCard)? null : Cards::get($this->commCard);
  }

  public function isCommPending()
  {
    return $this->commPending == 1;
  }


  public function canCommunicate()
  {
    $mission = Missions::getCurrent();
    return $this->commToken != 'used' && is_null($this->commCard) && !$mission->isDisrupted(); // TODO && $mission->canCommunicate($this->id);
  }

  public function winTrick()
  {
    self::DB()->inc(['player_trick_number' => 1], $this->id);
  }

  public function toggleComm()
  {
    $this->commPending = 1 - $this->commPending;
    self::DB()->update(['comm_pending' => $this->commPending], $this->id);
  }

  public function communicate($cardId, $status)
  {
    $this->commCard = $cardId;
    $this->commToken = $status;
    self::DB()->update([
      'comm_card_id' => $cardId,
      'comm_token' => $status,
    ], $this->id);
  }

  public function usedComm()
  {
    $this->commCard = null;
    $this->commToken = 'used';
    self::DB()->update([
      'comm_card_id' => null,
      'comm_token' => 'used',
    ], $this->id);
  }

  // Choose distress signal direction
  public function chooseDirection($dir)
  {
    $this->distressChoice = $dir;
    self::DB()->update(['distress_choice' => $dir], $this->id);
  }

  public function setDistressCard($cardId)
  {
    $this->distressCard = $cardId;
    self::DB()->update(['distress_card_id' => $cardId], $this->id);
  }

  // Save reply at the given question
  public function reply($i)
  {
    self::DB()->update(['reply_choice' => $i], $this->id);
  }
}
