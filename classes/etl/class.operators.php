<?php

class OperatorTransformation extends Transformer{
  
  /**
   *
   */
  public function writeOperatorsToEtl(){
    $this->printer->output( '<h1>Operator Translation</h1>' );
    
    $insert_queries['gb'] = 
    'INSERT INTO merge_operators (OperatorID, OName, OSurname, Salary, OfficeId)
     SELECT OperatorID, OName, OSurname, Salary, OfficeId FROM gb_operators';
    
    $insert_queries['mt'] = 
    'INSERT INTO merge_operators (OperatorID, OName, OSurname, Salary, OfficeId)
     SELECT OperatorID, OName, OSurname, Salary, OfficeId FROM mt_operators';
    
    $insert_queries['vi'] = 
    'INSERT INTO merge_operators (OperatorID, OName, OSurname, Salary, OfficeId)
     SELECT OperatorID, OName, OSurname, Salary, OfficeId FROM vi_operators';
    
    
    foreach( $insert_queries as $insert_query ){
      $this->mw_import->executeQuery( $insert_query );
      
    }
  }
  
  
  
}
