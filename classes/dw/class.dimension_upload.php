<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : create manager hierarchy
Description   : 
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

class DWUpload extends Transformer{
  
  private $insert_queries = array();
  /**
   *
   */
  public function write_to_warehouse(){
    $this->write_operator_office_query();
    $this->write_hotel_query();
    $this->write_customer_query();
    $this->write_manager_query();
    $this->write_location_query();
    $this->run_queries();
  }
  
  /**
   *
   */
  public function run_queries(){
    foreach ($this->insert_queries as $query){
      $this->mw_import->executeQuery($query);
    }
  }
  /**
   *
   */
  public function write_operator_office_query(){
    $this->insert_queries['operator'] = sprintf( 
      'INSERT INTO dwarehouse.dim_operator (operator_id, operator_name, operator_surname, operator_salary, office_name, office_type)
      SELECT SOperatorsID, OName, OSurname, Salary, OfficeName, OType FROM merge_operators_offices');
  }
  
  public function write_hotel_query(){
    $this->insert_queries['hotel'] = sprintf( 
      'INSERT INTO dwarehouse.dim_hotel (hotel_id, hotel_name, num_rooms, hotel_category)
      SELECT SHotelID, Hname, NRoom, Category FROM merge_hotel');
  }
  
  public function write_customer_query(){
    $this->insert_queries['customer'] = sprintf( 
      'INSERT INTO dwarehouse.dim_customer (customer_id, customer_name, customer_surname, customer_age, customer_gender)
      SELECT SCustomerID, Cname, Csurname, Age, Gender FROM merge_customers');
  }
  
  public function write_manager_query(){
    $this->insert_queries['manager'] = sprintf( 
      'INSERT INTO dwarehouse.dim_manager (manager_id, manager_name, manager_surname )
      SELECT ManagerID, ManagerName, ManagerSurname FROM merge_hotel_manager');
  }
  
  public function write_location_query(){
    $this->insert_queries['location'] = sprintf( 
      'INSERT INTO dwarehouse.dim_location (location_id, city_name, region_name, country_name, continent_name )
      SELECT LocationID, CityName, RegionName, CountryName, ContinentName FROM merge_location');
  }
  
  /*public function write_date_query(){
    $this->insert_queries['date'] = sprintf( 
      'INSERT INTO dwarehouse.dim_hotel (hotel_id, hotel_name, num_rooms, hotel_category)
      SELECT SHotelID, Hname, NRoom, Category FROM merge_hotel');
  }*/
  
  
  
}
