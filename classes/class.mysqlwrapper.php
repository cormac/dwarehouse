<?php

class MysqlWrapper{


  private $link;
  private $result;
  private $db;
/**
 *
 */
  public function __construct($db){
    $this->db = $db;
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
    
  }
  
  public function __get($key) {
    return $this->$key;
  }
}
