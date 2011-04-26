<?php

class MysqlWrapper{


  private $link;
  private $result;
  private $db;
  private $printer;
  
/**
 *
 */
  public function __construct( $db, $verbose = true ){
    $this->db = $db;
    $this->printer = new Printer( $verbose );
    $this->openConnection();
  }
  public function openConnection(){
    $this->link = mysql_connect( 'localhost', 'root', 'edils0424' );
    mysql_select_db( $this->db, $this->link );
  }
  
  public function closeConnection(){
    mysql_close( $this->link );
  }
  
  public function executeQuery( $query ){
    $this->result = mysql_query( $query, $this->link );
    $this->printer->output( $query . '<br/>');
  }
  
  public function __get($key) {
    return $this->$key;
  }
}
