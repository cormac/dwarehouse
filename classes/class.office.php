<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 23/04/11
Description   : Perform transformations on the hotel table
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

class OfficeTransformation{
  private $mw_import;
    
  
  public function __construct(){
    $this->mw_import = new MysqlWrapper('dw_import');
    $this->mw_import->openConnection();
  }
  
  
  /*
  write goodby.com hotel table data to the merge table   
  */
  public function writeGBOfficeToEtl(){
    
    //WRITE TO MERGE TABLES
    echo('<h3> write to merge </h3>' );
    $merge_query = 'INSERT INTO merge_offices (OfficeId, OfficeName, OAddress, CityID, OType) 
    SELECT  OfficeId, OfficeName, OAddress, CityID, FROM gb_offices';
    
    print('<h3>write to merge gb</h3>'); 
    print($merge_query); 
    
    $this->mw_import->executeQuery( $merge_query );
  }
  
  public function writeMTOfficeToEtl(){
    // IMPORT THE DATA FROM ORIGINAL TABLE
    
    
    //PERFORM TRANSFORMATIONS
    
    
    //WRITE TO MERGE TABLES
    $merge_query = 'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID FROM mt_hotel';
    
    print('<h3>write to merge mt</h3>'); 
    print($merge_query); 
    
    $this->mw_import->executeQuery( $merge_query );
     
  }
  
  public function writeViaggiOfficeToEtl(){
    
    
    //WRITE TO MERGE TABLES
    echo('<h3> write to merge </h3>' );
    $merge_query = 'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID FROM temp_viaggi_hotel';
    
    print('<h3>write to merge gb</h3>'); 
    print($merge_query); 
    
    $this->mw_import->executeQuery( $merge_query );
     
  }
  
  
  /**
   *
   */
    public function cleanUp(){
    
    $this->mw_import->closeConnection();
    return 'cleanUp';
  }


}


