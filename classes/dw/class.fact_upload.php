<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : upload fact data
Description   : 
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

class FactUpload extends  Transformer{
  
  
  private $insert_query = '
  INSERT INTO dwarehouse.holiday_detail_fact_table(hotel_id, customer_id, operator_id, manager_id, total_price, nights, persons, hotel_location_id, operator_location_id )
  SELECT merge_hotel.SHotelID AS hotel_id, merge_customers.SCustomerID AS customer_id, merge_operators_offices.SOperatorsID AS operator_id,
  merge_hotel.ManagerID AS manager_id, merge_holiday_details.Nigths * merge_holiday_details.Price_per_night + merge_holiday_details.Price_Extra  AS total_price, 
  Nigths AS nights, Persons AS persons, merge_hotel.LocationID AS hotel_location_id, merge_operators_offices.LocationID AS operator_location_id
  FROM merge_holiday_details 
  LEFT JOIN merge_holiday ON merge_holiday.HolIDayID = merge_holiday_details.HolIDayID
  LEFT JOIN merge_customers ON merge_customers.customerID = merge_holiday.CustomerID
  LEFT JOIN merge_hotel ON merge_hotel.HotelID = merge_holiday_details.HotelID
  LEFT JOIN merge_operators_offices ON merge_operators_offices.operatorID = merge_holiday.operatorID
  LEFT JOIN merge_hotel_manager ON merge_hotel.ManagerID = merge_hotel_manager.ManagerID
  WHERE merge_holiday.origin = %d
  AND merge_holiday_details.origin = %d
  AND merge_customers.origin = %d
  AND merge_operators_offices.origin = %d
  AND merge_hotel.origin = %d';
  
  private $insert_queries = array();
  /**
   *
   */
  public function write_facts(){
    $this->write_vi_facts();
    $this->write_mt_facts();
    $this->write_gb_facts();
    $this->run_queries();
  }
  
  public function run_queries(){
    foreach ($this->insert_queries as $query){
      $this->mw_import->executeQuery($query);
    }
  }
  
  /**
   *
   */
  public function write_vi_facts(){
    $this->insert_queries['vi'] = sprintf($this->insert_query, VI, VI, VI, VI, VI);
  }
  
  public function write_mt_facts(){
    $this->insert_queries['mt'] = sprintf($this->insert_query, MT, MT, MT, MT, MT);
  }
  
  public function write_gb_facts(){
    $this->insert_queries['gb'] = sprintf($this->insert_query, GB, GB, GB, GB, GB);
  }

}
