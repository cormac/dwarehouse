<?php

class Transformer{

  /**
   *
   */
  protected $printer;
  protected $mw_import;
  
  public function __construct($verbose = true){
    $this->mw_import = new MysqlWrapper( 'dw_import', $verbose );
    $this->printer = new Printer($verbose);

  }


  /**
   *
   */
    public function cleanUp(){
    
    $this->mw_import->closeConnection();
    return 'cleanUp';
  }
}
