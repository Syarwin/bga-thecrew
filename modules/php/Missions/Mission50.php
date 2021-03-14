<?php
namespace CREW\Missions;
use \CREW\Game\Globals;
use \CREW\Game\Notifications;
use \CREW\Game\Players;


class Mission50 extends AbstractMission
{
  public function __construct(){
    $this->id = 50;
    $this->desc = clienttranslate('The way back is much more complicated than expected. Some modules have been permanently damaged during the trip and you will have to fight against the immense gravitational pull of the Sun. With your reserves depleted, you cannot allow yourself any mistakes — the voyage home must be executed precisely. You must first take advantage of a gravitational catapult. Then, the ship’s modules must be kept under control and the approach to Earth initiated. In the end, landing on Earth will require no less attention than any other maneuver you’ve performed so far. <b>Everyone looks at his or her cards. A crew member must win the first four tricks. Another crew member has to win the last trick. The remaining crew members must win all the tricks in between. Your commander asks everyone for his or her preferred task, you then decide together as a crew who should assume which role. Do not say anything about your cards. </b>');

    $this->question = clienttranslate('What is your preferred task?');
    $this->replies = [clienttranslate('Only the first four tricks'), clienttranslate('All tricks in between'), clienttranslate('Only the last trick') ];

    $this->informations = [
      'special' => clienttranslate('Only the first four tricks'),
      'specialTooltip' => clienttranslate('This crew member must win only the first four tricks'),
    ];
  }

  public function getTargetablePlayers($removeCommander = false)
  {
    // Allow the commander to target himself
    return parent::getTargetablePlayers(false);
  }


  // Double pick crew process
  public function pickCrew($crewId)
  {
    $crew = Players::get($crewId);
    $player = Players::getCurrent();

    if(Globals::getSpecial() == 0){
      Globals::setSpecial($crewId);
      Notifications::specialMemberMission50($crew);
      return 'pickCrew';
    } else {
      Globals::setSpecial2($crewId);
      Notifications::special2MemberMission50($crew);
      return null;
    }
  }


  function check($lastTrick)
  {
    $trickCount = Globals::getTrickCount();
    if($trickCount <= 4 XOR $lastTrick['winner']->isSpecial()){
      $this->fail(); // First 4 tricks must be won by special member and he must wins nothing else
    }
    else if(Globals::isLastTrick() XOR $lastTrick['winner']->isSpecial2()){
      $this->fail(); // Last trick must be won by special 2 member, and he must wins nothing else
    }
    else if(Globals::isLastTrick()){
      $this->success();
    }
    else {
      $this->continue();
    }
  }
}
