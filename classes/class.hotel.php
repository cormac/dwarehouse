<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 23/04/11
Description   : Perform transformations on the hotel table
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

class HotelTransformation{
  private $mw_import;
    
  
  public function __construct(){
    $this->mw_import = new MysqlWrapper('dw_import');
    $this->mw_import->openConnection();
  }
  
  
  /*
  write goodby.com hotel table data to the merge table   
  */
  public function writeGBHotelToEtl(){
    // IMPORT THE DATA FROM ORIGINAL TABLE
    $insert_query = 'INSERT INTO temp_gb_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, str_category, ManagerID) 
    SELECT CONVERT(HotelID, SIGNED) AS hotel, Hname, HAddress, Telephone, NRoom, CityID, Category, (SELECT ManagerID FROM mt_hotelmanager WHERE ManagerName =\'Mary\' AND ManagerSurname=\'Summer\' ) as ManagerID FROM gb_hotel';
    
    echo ('<h3> Insert </h3><p>Note that although stated in the assignment that hotelID was a string the import provides it as an integer</p>' . $insert_query);
    $this->mw_import->executeQuery( $insert_query );
    
    //PERFORM TRANSFORMATIONS
    echo ('<h3> Transform </h3>');
    for($i=1; $i<6; $i++){
      $update_queries['category_' . $i] = sprintf('UPDATE temp_gb_hotel SET Category = %d WHERE str_category = \'%s star\';', $i, $i);
    }
    
    print('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
    
       $this->mw_import->executeQuery( $update_query );
       print($update_query . '<br/>');
       
    }
    
    //WRITE TO MERGE TABLES
    echo('<h3> write to merge </h3>' );
    $merge_query = 'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID FROM temp_gb_hotel';
    
    print('<h3>write to merge gb</h3>'); 
    print($merge_query); 
    
    $this->mw_import->executeQuery( $merge_query );
  }
  
  public function writeMTHotelToEtl(){
    // IMPORT THE DATA FROM ORIGINAL TABLE
    
    
    //PERFORM TRANSFORMATIONS
    
    
    //WRITE TO MERGE TABLES
    $merge_query = 'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID FROM mt_hotel';
    
    print('<h3>write to merge mt</h3>'); 
    print($merge_query); 
    
    $this->mw_import->executeQuery( $merge_query );
     
  }
  
  public function writeViaggiHotelToEtl(){
    // IMPORT THE DATA FROM ORIGINAL TABLE
      // IMPORT THE DATA FROM ORIGINAL TABLE
    $insert_query = 'INSERT INTO temp_viaggi_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, str_category, ManagerID) 
    SELECT HotelID AS hotel, Hname, HAddress, Telephone, NRoom, CityID, Category, (SELECT ManagerID FROM mt_hotelmanager WHERE ManagerName =\'John\' AND ManagerSurname=\'Smith\' ) as ManagerID FROM vi_hotel';
    
    
    echo ('<h3> Insert </h3>' . $insert_query);
    $this->mw_import->executeQuery( $insert_query );
    
    //PERFORM TRANSFORMATIONS
    echo ('<h3> Transform </h3>');
    for($i=1; $i<6; $i++){
      $update_queries['category_' . $i] = sprintf('UPDATE temp_viaggi_hotel SET Category = %d WHERE str_category = \'%s star\';', $i, $i);
    }
    
    print('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
    
       $this->mw_import->executeQuery( $update_query );
       print($update_query . '<br/>');
       
    }
    
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


