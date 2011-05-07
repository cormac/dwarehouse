<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 
Description   : moveoperators and managers to etl tables
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/


class OperatorTransformation extends Transformer{
  
  /**
   *
   */
  public function writeOperatorsToEtl(){
    $this->printer->output( '<h1>Operator and Manager Translation</h1>' );
    
    $insert_queries['gb'] = 
    'INSERT INTO merge_operators (OperatorID, OName, OSurname, Salary, OfficeId)
     SELECT OperatorID, OName, OSurname, Salary, OfficeId FROM gb_operators';
    
    $insert_queries['mt'] = 
    'INSERT INTO merge_operators (OperatorID, OName, OSurname, Salary, OfficeId)
     SELECT OperatorID, OName, OSurname, Salary, OfficeId FROM mt_operators';
    
    $insert_queries['vi'] = 
    'INSERT INTO merge_operators (OperatorID, OName, OSurname, Salary, OfficeId)
     SELECT OperatorID, OName, OSurname, Salary, OfficeId FROM vi_operators';
    
    $insert_queries['mt_managers'] = 
    'INSERT INTO merge_hotel_manager( ManagerID, ManagerName, ManagerSurname, ResponsibleID )
    SELECT ManagerID, ManagerName, ManagerSurname, ResponsibleID FROM mt_hotelmanager';
    
    $insert_queries['manager_update'] = 'UPDATE merge_hotel_manager SET ResponsibleID = NULL WHERE ManagerID=\'C000000001\';';
    foreach( $insert_queries as $insert_query ){
      $this->mw_import->executeQuery( $insert_query );
      
    }
  }
  
  
  
}
