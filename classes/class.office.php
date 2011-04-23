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

class OfficeTransformation{
  private $mw_import;
  private $printer;
  
  public function __construct( $verbose = true ){
    $this->printer = new Printer($verbose);
    $this->printer->output( '<h1>Office Translation</h2>' );
    $this->mw_import = new MysqlWrapper('dw_import');

  }
  
  
  /*
  write goodby.com hotel table data to the merge table   
  */
  public function writeGBOfficeToEtl(){
    $this->printer->output('<h2>GoodBye.com</h2>');
    //WRITE TO MERGE TABLES
    $this->printer->output('<h3> write to temp </h3>' );
    $input_query = 'INSERT INTO temp_gb_offices (OfficeId, OfficeName, OAddress, int_CityID, int_mt_cityID, OType) 
    SELECT gb_offices.OfficeId, gb_offices.OfficeName, gb_offices.OAddress, gb_offices.CityID, mt_city.CityID, 0 AS OType FROM gb_offices
    JOIN gb_city ON gb_offices.CityID = gb_city.CityID
    JOIN mt_city ON gb_city.CityName = mt_city.CityName;
    ';
    //PERFORM TRANSFORMATIONS
    $update_query = 'UPDATE temp_gb_offices SET CityID = int_mt_cityID;';
    
    $merge_query = 'INSERT INTO merge_offices (OfficeId, OfficeName, OAddress, CityID, OType) 
    SELECT  OfficeId, OfficeName, OAddress, CityID, Otype FROM temp_gb_offices';
    
    $this->printer->output($input_query); 
    $this->mw_import->executeQuery( $input_query );
    
    $this->printer->output('<h3>Transformations gb</h3>');
    $this->printer->output($update_query); 
    $this->mw_import->executeQuery( $update_query );
    
    
    $this->printer->output('<h3>write to merge gb</h3>');
    $this->printer->output( $merge_query );
    $this->mw_import->executeQuery($merge_query);
    
  }
  
  public function writeMTOfficeToEtl(){
    
    $this->printer->output('<h2>My Travel</h2>');


    //WRITE TO MERGE TABLES
    $merge_query = 'INSERT INTO merge_offices (OfficeId, OfficeName, OAddress, CityID, OType) 
    SELECT  OfficeId, OfficeName, OAddress, CityID, Otype FROM mt_offices';
    
    $this->printer->output('<h3>write to merge_offices mt</h3>'); 
    $this->printer->output($merge_query); 
    
    $this->mw_import->executeQuery( $merge_query );
     
  }
  
  public function writeViaggiOfficeToEtl(){
    $this->printer->output('<h2>Viaggi</h2>');


    // IMPORT THE DATA FROM ORIGINAL TABLE
    
    $this->printer->output('<h3> write to temp vi</h3>' );
    $input_query = 'INSERT INTO temp_vi_offices (OfficeId, OfficeName, OAddress, int_CityID, int_mt_cityID, OType) 
    SELECT vi_offices.OfficeId, vi_offices.OfficeName, vi_offices.OAddress, vi_offices.CityID, mt_city.CityID, 1 AS OType FROM vi_offices
    JOIN vi_city ON vi_offices.CityID = vi_city.CityID
    JOIN mt_city ON vi_city.CityName = mt_city.CityName;
    ';
    $update_query = 'UPDATE temp_vi_offices SET CityID = int_mt_cityID;';
    $merge_query = 'INSERT INTO merge_offices (OfficeId, OfficeName, OAddress, CityID, OType) 
    SELECT  OfficeId, OfficeName, OAddress, CityID, Otype FROM temp_vi_offices';
 
    $this->printer->output($input_query); 
    $this->mw_import->executeQuery( $input_query );
    //PERFORM TRANSFORMATIONS
    
    
    $this->printer->output('<h3>transform vi</h3>');     
    $this->printer->output($update_query); 
    $this->mw_import->executeQuery( $update_query );
    
    //WRITE TO MERGE TABLES
    $this->printer->output('<h3>write to merge vi</h3>');
    $this->printer->output( $merge_query );
    $this->mw_import->executeQuery($merge_query);
  }
  
  
  /**
   *
   */
    public function cleanUp(){
    
    $this->mw_import->closeConnection();
    return 'cleanUp';
  }


}


