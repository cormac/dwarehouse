<?php

class CreateDwTables{

  private $mw_import;
  private $tables;
  private $create_sql;
  private $temp_table_sql;
  private $error_tables;
  private $verbose;
  private $printer;
  
  public function __construct($verbose = true){
    $this->mw_import = new MysqlWrapper('dw_import', $verbose);
    $this->verbose = $verbose;
    $this->printer = new Printer($verbose);
  }
  
  /**
   *
   */  
  private function clearTables($tables, $label){
    $this->printer->output( '<h3>' . $label . '</h3>' );
    foreach ($tables as $key => $statement){
      $clear_statement = sprintf('DROP TABLE IF EXISTS %s;', $key);
      $this->mw_import->executeQuery( $clear_statement );
    }
    
  }
  
  /**
   *
   */
  private function output($text){
    if($verbose)print($text);
  }
  
  /**
   *
   */
  /*****************************************************************************************************************
  
    CREATE TABLES
  
  *****************************************************************************************************************/
  public function buildCreateStatements(){
  
    $this->printer->output( '<h1>Warehouse Tables</h1>' );
    
    $this->create_sql['dw_manager_hierarchy'] = 
    'CREATE TABLE dw_manager_hierarchy(
      id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
      parent varchar(10),
      child varchar(10),
      levels int,
      bottom tinyint,
      top tinyint
    )';
   
    
    
    
    $this->clearTables($this->create_sql, 'Clear the warehouse tables' );
    
    $this->printer->output('<h3>Create the warehouse tables</h3>');
    foreach ($this->create_sql as $create_query)
    {
       $this->mw_import->executeQuery( $create_query );
    }
   
    
  }
  
  
  
  public function cleanUp(){
    
    $this->mw_import->closeConnection();
    return 'cleanUp';
  }
  
  
  
}
