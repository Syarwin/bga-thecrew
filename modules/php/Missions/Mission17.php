<?php
namespace CREW\Missions;

class Mission17 extends AbstractMission
{
  public function __construct(){
    $this->id = 17;
    $this->desc = clienttranslate('The damage to the ninth control module is even worse than originally thought, so you will have to invest significantly more time in making the repair. At the same time, however, you still have to track your course and send a message back to Earth where they are eagerly awaiting your status. <b>You still must not win a trick with a nine.</b>');
    $this->tasks = 2;
    $this->informations = [
      'cards' => clienttranslate('No Trick with'),
      'cardsType' => 'nines',
    ];
  }

  public function check($lastTrick)
  {
    // Winning card is a 9 => fail
    if($lastTrick['bestCard']['value'] == 9){
      $this->fail();
    } else {
      // Otherwise, do the check of tasks
      parent::check($lastTrick);

      // If all tasks are satisfied, we must wait all the 9 to be played
      $nines = Cards::getRemeaningOfValue(9);
      if(self::getStatus() == MISSION_SUCCESS && !empty($nines)){
        $this->continue();
      }
    }
  }
}
