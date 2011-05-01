<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 23/04/11
Description   : Perform transformations on the office table - city ID needs to be updated to match mytravel
                Office type needs to be created for gb and vi 
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

class OfficeTransformation extends Transformer{

  
  
  
  /*
  write goodby.com hotel table data to the merge table   
  */
  public function writeGBOfficeToEtl(){
    $this->printer->output( '<h1>Office Translation</h1>' );
    $this->printer->output('<h2>GoodBye.com</h2>');
    //WRITE TO MERGE TABLES
    $this->printer->output('<h3> write to temp </h3>' );
    $input_query = 'INSERT INTO merge_offices (OfficeId, OfficeName, OAddress, CityID, OType) 
    SELECT gb_offices.OfficeId, gb_offices.OfficeName, gb_offices.OAddress, mt_city.CityID, 0 AS OType FROM gb_offices
    JOIN gb_city ON gb_offices.CityID = gb_city.CityID
    JOIN mt_city ON gb_city.CityName = mt_city.CityName;
    ';
    
    $this->mw_import->executeQuery( $input_query );
    
  }
  
  public function writeMTOfficeToEtl(){
    $this->printer->output('<h2>My Travel</h2>');

    //WRITE TO MERGE TABLES
    $merge_query = 'INSERT INTO merge_offices (OfficeId, OfficeName, OAddress, CityID, OType) 
    SELECT  OfficeId, OfficeName, OAddress, CityID, Otype FROM mt_offices';
    $this->printer->output('<h3>write to merge_offices mt</h3>'); 
    $this->mw_import->executeQuery( $merge_query );
     
  }
  
  public function writeViaggiOfficeToEtl(){
    $this->printer->output('<h2>Viaggi</h2>');


    // IMPORT THE DATA FROM ORIGINAL TABLE
    $this->printer->output('<h3> write to temp vi</h3>' );
    $input_query = 'INSERT INTO merge_offices (OfficeId, OfficeName, OAddress,  CityID, OType) 
    SELECT vi_offices.OfficeId, vi_offices.OfficeName, vi_offices.OAddress,  mt_city.CityID, 1 AS OType FROM vi_offices
    JOIN vi_city ON vi_offices.CityID = vi_city.CityID
    JOIN mt_city ON vi_city.CityName = mt_city.CityName;
    ';
    
    $this->mw_import->executeQuery( $input_query );

  }
  
  



}


