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
  private $printer;
  
  public function __construct($verbose = true){
    $this->mw_import = new MysqlWrapper('dw_import');
    $this->printer = new Printer($verbose);
  }
  
  
  /*
  write goodby.com hotel table data to the merge table   
  */
  public function writeGBHotelToEtl(){
    $this->printer->output( '<h2>GoodBye.com</h2>' );
    // IMPORT THE DATA FROM ORIGINAL TABLE
    $insert_query = 'INSERT INTO temp_gb_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, int_mt_cityID, str_category, ManagerID) 
    SELECT CONVERT(HotelID, SIGNED) AS hotel, Hname, HAddress, Telephone, NRoom, gb_hotel.CityID, mt_city.CityID, Category, 
    (SELECT ManagerID FROM mt_hotelmanager WHERE ManagerName =\'Mary\' AND ManagerSurname=\'Summer\' ) as ManagerID 
    FROM gb_hotel
    JOIN gb_city ON gb_city.CityID = gb_hotel.CityID
    JOIN mt_city ON gb_city.CityName = mt_city.CityName';
    
    $this->printer->output ('<h3> Insert </h3><p>Note that although stated in the assignment that hotelID was a string the import provides it as an integer</p>' . $insert_query);
    $this->mw_import->executeQuery( $insert_query );
    
    //PERFORM TRANSFORMATIONS
    $this->printer->output ('<h3> Transform </h3>');
    for($i=1; $i<6; $i++){
      $update_queries['category_' . $i] = sprintf('UPDATE temp_gb_hotel SET Category = %d WHERE str_category = \'%s star\';', $i, $i);
    }
    $update_queries['cityID'] = 'UPDATE temp_gb_hotel SET CityID=int_mt_cityID';
    $this->printer->output('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
    
       $this->mw_import->executeQuery( $update_query );
       $this->printer->output($update_query . '<br/>');
       
    }
    
    //WRITE TO MERGE TABLES
    $this->printer->output('<h3> write to merge </h3>' );
    $merge_query = 'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID FROM temp_gb_hotel';
    
    $this->printer->output('<h3>write to merge gb</h3>'); 
    $this->printer->output($merge_query); 
    
    $this->mw_import->executeQuery( $merge_query );
  }
  
  public function writeMTHotelToEtl(){
    $this->printer->output('<h2>My Travel</h2>');
    // IMPORT THE DATA FROM ORIGINAL TABLE
    
    
    //PERFORM TRANSFORMATIONS
    
    
    //WRITE TO MERGE TABLES
    $merge_query = 'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID FROM mt_hotel';
    
    $this->printer->output('<h3>write to merge mt</h3>'); 
    $this->printer->output($merge_query); 
    
    $this->mw_import->executeQuery( $merge_query );
     
  }
  
  public function writeViaggiHotelToEtl(){
    $this->printer->output('<h2>Viaggi</h2>');
      // IMPORT THE DATA FROM ORIGINAL TABLE
    $insert_query = 'INSERT INTO temp_viaggi_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, int_mt_cityID, str_category, ManagerID) 
    SELECT HotelID AS hotel, Hname, HAddress, Telephone, NRoom, vi_hotel.CityID, mt_city.CityID, Category, 
      (SELECT ManagerID FROM mt_hotelmanager WHERE ManagerName =\'John\' AND ManagerSurname=\'Smith\' ) as ManagerID
    FROM vi_hotel
    JOIN vi_city ON vi_city.CityID = vi_hotel.CityID
    JOIN mt_city ON vi_city.CityName = mt_city.CityName';
    
    
    $this->printer->output ('<h3> Insert </h3>' . $insert_query);
    $this->mw_import->executeQuery( $insert_query );
    
    //PERFORM TRANSFORMATIONS
    $this->printer->output ('<h3> Transform </h3>');
    for($i=1; $i<6; $i++){
      $update_queries['category_' . $i] = sprintf('UPDATE temp_viaggi_hotel SET Category = %d WHERE str_category = \'%s star\';', $i, $i);
    }
    $update_queries['cityID'] = 'UPDATE temp_viaggi_hotel SET CityID=int_mt_cityID';
    $this->printer->output('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
    
       $this->mw_import->executeQuery( $update_query );
       $this->printer->output($update_query . '<br/>');
       
    }
    
    //WRITE TO MERGE TABLES
    $this->printer->output('<h3> write to merge </h3>' );
    $merge_query = 'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID FROM temp_viaggi_hotel';
    
    $this->printer->output('<h3>write to merge gb</h3>'); 
    $this->printer->output($merge_query); 
    
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


