<?php
namespace CREW\Game;
use thecrewleocaseiro;

class UserException extends \BgaUserException {
  public function __construct($str)
  {
    parent::__construct(thecrewleocaseiro::translate($str));
  }
}
?>
