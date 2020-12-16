<?php
namespace CREW\Game;
use thecrew;

class UserException extends \BgaUserException {
  public function __construct($str)
  {
    parent::__construct(thecrew::translate($str));
  }
}
?>
