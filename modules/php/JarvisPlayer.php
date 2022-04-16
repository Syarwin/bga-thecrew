<?php
namespace CREW;
use CREW\Game\Globals;
use CREW\Game\GlobalsVars;
use CREW\Cards;
use CREW\Tasks;
use CREW\Helpers\Utils;
use CREW\Helpers\Collection;

class JarvisPlayer
{
  public function __construct()
  {
    $this->id = \JARVIS_ID;
    $this->no = 99;
    $this->name = 'Jarvis';
    $this->color = 'black';
    $this->score = 1;
    $this->eliminated = false;
    $this->zombie = false;
    $this->nTricks = '0';
    $this->commCard = null;
    $this->commToken = 'middle';
    $this->commPending = 0;
    $this->distressChoice = '0';
    $this->distressCard = null;
    $this->reply = null;
    $this->distressAuto = '0';
    $this->continueAuto = '0';
    $this->preselectedCard = null;
  }

  private $id;
  private $no;
  private $name;
  private $color;
  private $score;
  private $eliminated;
  private $zombie;
  private $nTricks;
  private $commCard;
  private $commToken;
  private $commPending;
  private $distressChoice;
  private $distressCard;
  private $reply;
  private $distressAuto;
  private $continueAuto;
  private $preselectedCard;

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

  public function isCommander() { return $this->id == Globals::getCommander(); }
  public function isSpecial() { return $this->id == Globals::getSpecial(); }
  public function isSpecial2() { return $this->id == Globals::getSpecial2(); }
  public function getDistressAuto(){ return $this->distressAuto; }
  public function getContinueAuto(){ return $this->continueAuto; }

  public function getUiData($pId)
  {
    $current = Globals::getCommander() == $pId;
    return [
      'id'        => $this->id,
      'no'        => $this->no,
      'name'      => $this->name,
      'color'     => $this->color,
      'score'     => $this->score,
      'nTricks'   => $this->nTricks,
      'cards'     => $this->getCards()->toArray(),
      'preselected' => $current ? $this->preselectedCard : null,
      'nCards'    => count($this->getCards()),
      'tasks'     => $this->getTasks($current),
      'table'     => $this->getOnTable(),
      'commToken' => $this->commToken,
      'commCard'  => $this->getCardOnComm(),
      'commPending' => $this->isCommPending(),
      'canCommunicate' => $this->canCommunicate(),
      'distressChoice' => $this->distressChoice,
      'distressCard' => $current ? GlobalsVars::getJarvisDistressCard() : null,
      'distressAuto' => $this->distressAuto,
      'reply' => $this->getReply(),
      'continueAuto' => $this->continueAuto,
      'afterPlayer' => GlobalsVars::getJarvisPlaysAfter(),
    ];
  }

  public function getCards($hidden = true, $column = null)
  {
    $filtered = new Collection();
    $cardList = GlobalsVars::getJarvisCardList();
    foreach ($cardList as $col => $cards) {
      foreach ($cards as $card) {
        $c = Cards::get($card['id']);
        $c['column'] = $col;
        $c['hidden'] = false;
        if ($card['hidden'] && $hidden) {
          $c['hidden'] = true;
          unset($c['value']);
          unset($c['color']);
          $c['id'] = 100 + $col;
        }

        if (is_null($column) || $col == $column) {
          $filtered[$c['id']] = $c;
        }
      }
    }
    return $filtered;
  }

  public function getRandomCard()
  {
    $cards = $this->getCards();
    if($this->commCard != null){
      $cards = array_filter((array) $cards, function($card) { return $card['id'] != $this->commCard; });
    }
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

  public function getPreselectedCard()
  {
    return $this->preselectCard;
  }


  public function isCommPending()
  {
    return $this->commPending;
  }


  public function canCommunicate()
  {
    return $this->commToken != 'used' && is_null($this->commCard);
  }

  public function winTrick()
  {
    GlobalsVars::setJarvisTricks((int) GlobalsVars::getJarvisTricks() + 1);
  }

  public function toggleComm()
  {
    // No need for Jarvis
    return;
  }

  public function communicate()
  {
    // No need for Jarvis
    return;
  }

  public function usedComm()
  {
    // No need for Jarvis
    return;
  }

  // Choose distress signal direction
  public function chooseDirection($dir)
  {
    // No need for Jarvis
    return;
  }

  public function setDistressCard($cardId)
  {
    // No need for Jarvis
    return;
  }

  public function setAutoPick($mode)
  {
    // No need for Jarvis
    return;
  }

  public function setAutoContinue($mode)
  {
    // No need for Jarvis
    return;
  }

  public function preselectCard($cardId)
  {
    $this->preselectedCard = $cardId;
  }

  public function clearPreselect()
  {
    $this->preselectedCard = null;
  }
  public function getTricksWon()
  {
    return GlobalsVars::getJarvisTricks();
  }

  // Save reply at the given question
  public function setReply($i)
  {
    GlobalsVars::setJarvisReply($i);
  }

  public function getReply()
  {
    GlobalsVars::getJarvisReply();
  }

  public function jsonSerialize($currentPlayerId = null)
  {
    $data = [];
    foreach ($this->attributes as $attribute => $field) {
      $data[$attribute] = $this->$attribute;
    }
    $data['nTricks'] = $this->getTricksWon();

    $current = Globals::getCommander() == $currentPlayerId;
    $data = array_merge($data, [
      'cards' => $this->getCards()->toArray(),
      'preselected' => $current ? $this->preselectedCard : null,
      'nCards' => $this->getCards()->count(),
      'tasks' => $this->getTasks($current)->toArray(),
      'table' => $this->getOnTable()->toArray(),
      'distressChoice' => $this->distressChoice,
      'distressCard' => $current ? GlobalsVars::getJarvisDistressCard() : null,
      'reply' => $this->getReply(),
      'id' => strval($this->id),
      'afterPlayer' => GlobalsVars::getJarvisPlaysAfter(),
    ]);

    return $data;
  }

}
