<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 23/04/11
Description   : Perform transformations on the hotel tables
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

class HotelTransformation extends Transformer{
  
  
  /*
  write goodby.com hotel table data to the merge table   
  */
  public function writeGBHotelToEtl(){
    $this->printer->output( '<h1>Hotel Translation</h2>' );
    $this->printer->output( '<h2>GoodBye.com</h2>' );
    // IMPORT THE DATA FROM ORIGINAL TABLE
    $insert_query = 
    'INSERT INTO temp_gb_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, str_category, ManagerID, SHotelManagerID, LocationID) 
    SELECT CONVERT(HotelID, SIGNED) AS hotel, Hname, HAddress, Telephone, NRoom, gb_city.CityID, Category, 
    (SELECT ManagerID FROM mt_hotelmanager WHERE ManagerName =\'Mary\' AND ManagerSurname=\'Summer\' ) as ManagerID, 
    (SELECT SHotelManagerID FROM merge_hotel_manager WHERE ManagerName =\'Mary\' AND ManagerSurname=\'Summer\' ) as SHotelManagerID, 
    LocationID 
    FROM gb_hotel
    LEFT JOIN gb_city ON gb_city.CityID = gb_hotel.CityID
    LEFT JOIN merge_location ON gb_city.CityName = merge_location.CityName;';
    
    $this->printer->output (
      '<h3> Insert </h3>
      <p>Note that although stated in the assignment that hotelID was a string the import provides it as an integer</p>'
    );
    
    $this->mw_import->executeQuery( $insert_query );
    
    //PERFORM TRANSFORMATIONS
    $this->printer->output ('<h3> Transform </h3>');
    for($i=1; $i<6; $i++){
      $update_queries['category_' . $i] = sprintf('UPDATE temp_gb_hotel SET Category = %d WHERE str_category = \'%s star\';', $i, $i);
    }
    
    $this->printer->output('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
       $this->mw_import->executeQuery( $update_query );   
    }
    
    //WRITE TO MERGE TABLES
    $this->printer->output('<h3> write to merge </h3>' );
    $merge_query = sprintf(
    'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID, SHotelManagerID, origin, LocationID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID, SHotelManagerID, %d AS origin, LocationID FROM temp_gb_hotel', GB );
    
    $this->printer->output('<h3>write to merge gb</h3>'); 
    
    $this->mw_import->executeQuery( $merge_query );
  }
  
  
  
  public function writeMTHotelToEtl(){
    $this->printer->output('<h2>My Travel</h2>');
    // IMPORT THE DATA FROM ORIGINAL TABLE
    
    
    //PERFORM TRANSFORMATIONS
    
    
    //WRITE TO MERGE TABLES
    $merge_query = sprintf(
    'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID, SHotelManagerID, origin, LocationID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, mt_hotel.CityID, Category, mt_hotel.ManagerID, SHotelManagerID, %d AS origin, LocationID FROM mt_hotel
    LEFT JOIN merge_location ON merge_location.CityID = mt_hotel.CityID
    JOIN merge_hotel_manager ON mt_hotel.ManagerID = merge_hotel_manager.ManagerID', MT );
    
    $this->printer->output('<h3>write to merge mt</h3>'); 
    
    $this->mw_import->executeQuery( $merge_query );
     
  }
  
  
  public function writeViaggiHotelToEtl(){
    $this->printer->output('<h2>Viaggi</h2>');
      // IMPORT THE DATA FROM ORIGINAL TABLE
    $insert_query = 'INSERT INTO temp_viaggi_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, str_category, ManagerID, SHotelManagerID, LocationID) 
    SELECT HotelID AS hotel, Hname, HAddress, Telephone, NRoom, merge_location.CityID, Category, 
    (SELECT ManagerID FROM mt_hotelmanager WHERE ManagerName =\'John\' AND ManagerSurname=\'Smith\' ) as ManagerID,
    (SELECT SHotelManagerID FROM merge_hotel_manager WHERE ManagerName =\'John\' AND ManagerSurname=\'Smith\' ) as SHotelManagerID, LocationID
    FROM vi_hotel
    LEFT JOIN vi_city ON vi_city.CityID = vi_hotel.CityID
    LEFT JOIN merge_location ON vi_city.CityName = merge_location.CityName';
    
    
    $this->mw_import->executeQuery( $insert_query );
    
    //PERFORM TRANSFORMATIONS
    $this->printer->output ('<h3> Transform </h3>');
    for($i=1; $i<6; $i++){
      $update_queries['category_' . $i] = sprintf('UPDATE temp_viaggi_hotel SET Category = %d WHERE str_category = \'%s star\';', $i, $i);
    }

    $this->printer->output('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
       $this->mw_import->executeQuery( $update_query );
    }
    
    //WRITE TO MERGE TABLES
    $this->printer->output('<h3> write to merge </h3>' );
    $merge_query = sprintf(
    'INSERT INTO merge_hotel (HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID, SHotelManagerID, origin, LocationID) 
    SELECT HotelID, Hname, HAddress, Telephone, NRoom, CityID, Category, ManagerID, %d AS origin, SHotelManagerID, LocationID FROM temp_viaggi_hotel', VI );
    $this->printer->output('<h3>write to merge gb</h3>'); 
    
    $this->mw_import->executeQuery( $merge_query );
     
  }
  
  /**
   *
   */
  private function set_location_id(){
    
  }


}


