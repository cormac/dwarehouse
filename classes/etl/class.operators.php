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
    $this->printer->output( '<h1>Operator, Office and Manager Translation</h1>' );
    
    $insert_queries['gb'] = sprintf(
    'INSERT INTO merge_operators_offices (OperatorID, OName, OSurname, Salary, OfficeName, CityID, OType, origin )
     SELECT OperatorID, OName, OSurname, Salary, OfficeName, mt_city.CityID, 0 AS OType, %d as origin FROM gb_operators
     LEFT JOIN gb_offices ON gb_offices.OfficeId = gb_operators.OfficeId
     LEFT JOIN gb_city ON gb_offices.CityID = gb_city.CityID
     LEFT JOIN mt_city ON gb_city.CityName = mt_city.CityName;', GB );
    
    $insert_queries['mt'] = sprintf(
    'INSERT INTO merge_operators_offices ( OperatorID, OName, OSurname, Salary, OfficeName, CityID, OType, origin )
     SELECT OperatorID, OName, OSurname, Salary, OfficeName, CityID, OType, %d as origin FROM mt_operators
     LEFT JOIN mt_offices ON mt_offices.OfficeId = mt_operators.OfficeId;', MT);
    
    $insert_queries['vi'] = sprintf(
    'INSERT INTO merge_operators_offices (OperatorID, OName, OSurname, Salary, OfficeName, CityID, OType, origin)
     SELECT OperatorID, OName, OSurname, Salary, OfficeName, mt_city.CityID, 1 AS OType, %d as origin FROM vi_operators
     LEFT JOIN vi_offices ON vi_offices.OfficeId = vi_operators.OfficeId
     LEFT JOIN vi_city ON vi_offices.CityID = vi_city.CityID
     LEFT JOIN mt_city ON vi_city.CityName = mt_city.CityName;', VI );
    
    $insert_queries['mt_managers'] = 
    'INSERT INTO merge_hotel_manager( ManagerID, ManagerName, ManagerSurname, ResponsibleID )
    SELECT ManagerID, ManagerName, ManagerSurname, ResponsibleID FROM mt_hotelmanager';
    
    $insert_queries['manager_update'] = 'UPDATE merge_hotel_manager SET ResponsibleID = NULL WHERE ManagerID=\'C000000001\';';
    foreach( $insert_queries as $insert_query ){
      $this->mw_import->executeQuery( $insert_query );
      
    }
  }
  
  
  
}
