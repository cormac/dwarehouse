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
    'INSERT INTO merge_operators_offices (OperatorID, OName, OSurname, Salary, OfficeName, CityID, OType, origin, LocationID )
     SELECT OperatorID, OName, OSurname, Salary, OfficeName, merge_location.CityID, 0 AS OType, %d as origin, LocationID FROM gb_operators
     LEFT JOIN gb_offices ON gb_offices.OfficeId = gb_operators.OfficeId
     LEFT JOIN gb_city ON gb_offices.CityID = gb_city.CityID
     LEFT JOIN merge_location ON gb_city.CityName = merge_location.CityName;', GB );
    
    $insert_queries['mt'] = sprintf(
    'INSERT INTO merge_operators_offices ( OperatorID, OName, OSurname, Salary, OfficeName, CityID, OType, origin, LocationID )
     SELECT OperatorID, OName, OSurname, Salary, OfficeName, merge_location.CityID, OType, %d as origin, LocationID FROM mt_operators
     LEFT JOIN mt_offices ON mt_offices.OfficeId = mt_operators.OfficeId
     LEFT JOIN merge_location ON merge_location.CityID = mt_offices.CityID;', MT);
    
    $insert_queries['vi'] = sprintf(
    'INSERT INTO merge_operators_offices (OperatorID, OName, OSurname, Salary, OfficeName, CityID, OType, origin, LocationID)
     SELECT OperatorID, OName, OSurname, Salary, OfficeName, merge_location.CityID, 1 AS OType, %d as origin, LocationID FROM vi_operators
     LEFT JOIN vi_offices ON vi_offices.OfficeId = vi_operators.OfficeId
     LEFT JOIN vi_city ON vi_offices.CityID = vi_city.CityID
     LEFT JOIN merge_location ON vi_city.CityName = merge_location.CityName;', VI );
    
    $insert_queries['mt_managers'] = 
    'INSERT INTO merge_hotel_manager( ManagerID, ManagerName, ManagerSurname, ResponsibleID )
    SELECT ManagerID, ManagerName, ManagerSurname, ResponsibleID FROM mt_hotelmanager';
    
    foreach( $insert_queries as $insert_query ){
      $this->mw_import->executeQuery( $insert_query );
      
    }
  }
  
  
  
}
