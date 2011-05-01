<?php

class Printer{
  
  private $verbose;
  /**
   *
   */
  function __construct($verbose = true){
    $this->verbose = $verbose;
  }
  
  /**
   *
   */
  public function output($text){
    if($this->verbose)print ($text);
  }
}
