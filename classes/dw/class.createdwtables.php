<?php

class CreateDwTables{

  private $mw_import;
  private $tables;
  private $create_sql;
  private $temp_table_sql;
  private $error_tables;
  private $verbose;
  private $printer;
  
  public function __construct($verbose = true){
    $this->mw_import = new MysqlWrapper('dw_import', $verbose);
    $this->verbose = $verbose;
    $this->printer = new Printer($verbose);
  }
  
  /**
   *
   */  
  private function clearTables($tables, $label){
    $this->printer->output( '<h3>' . $label . '</h3>' );
    foreach ($tables as $key => $statement){
      $clear_statement = sprintf('DROP TABLE IF EXISTS %s;', $key);
      $this->mw_import->executeQuery( $clear_statement );
    }
    
  }
  
  /**
   *
   */
  private function output($text){
    if($verbose)print($text);
  }
  
  /**
   *
   */
  /*****************************************************************************************************************
  
    CREATE TABLES
  
  *****************************************************************************************************************/
  public function buildCreateStatements(){
  
    $this->printer->output( '<h1>Warehouse Tables</h1>' );
    
    
    $this->create_sql['dim_hotel'] = 
    'CREATE  TABLE IF NOT EXISTS `dwarehouse`.`dim_hotel` (
    `hotel_id` INT NOT NULL AUTO_INCREMENT ,
    `hotel_name` VARCHAR(45) NULL ,
    `num_rooms` INT NULL ,
    `hotel_category` INT NULL  ,
    PRIMARY KEY (`hotel_id`) )';
    
    $this->create_sql['dim_manager'] = 
    'CREATE  TABLE IF NOT EXISTS `dwarehouse`.`dim_manager` (
    `manager_id` INT NOT NULL AUTO_INCREMENT ,
    `manager_name` VARCHAR(45) NULL ,
    `manager_surname` VARCHAR(45) NULL ,
    PRIMARY KEY (`manager_id`) )';
    
    
    $this->create_sql['manager_bridge'] = 
    'CREATE  TABLE IF NOT EXISTS `dwarehouse`.`manager_bridge` (
    `manager_id` INT NOT NULL ,
    `parent` INT NULL ,
    `child` INT NULL ,
    `levels` INT NULL ,
    `bottom` TINYINT NULL ,
    `top` TINYINT NULL ,
    INDEX `fk_manager_bridge_dim_manager1` (`manager_id` ASC) ,
    CONSTRAINT `fk_manager_bridge_dim_manager1`
    FOREIGN KEY (`manager_id` )
    REFERENCES `dwarehouse`.`dim_manager` (`manager_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)';
    
    $this->create_sql['dim_location'] = 
    'CREATE  TABLE IF NOT EXISTS `dwarehouse`.`dim_location` (
    `location_id` INT NOT NULL ,
    `city_name` VARCHAR(45) NULL,
    `region_name` VARCHAR(45) NULL ,
    `country_name` VARCHAR(45) NULL ,
    `continent_name` VARCHAR(45) NULL ,
    PRIMARY KEY (location_id) )';
    
    
    
    $this->create_sql['dim_operator'] = 
    'CREATE  TABLE IF NOT EXISTS `dwarehouse`.`dim_operator` (
    `operator_id` INT NOT NULL ,
    `operator_name` VARCHAR(45) NULL ,
    `operator_surname` VARCHAR(45) NULL ,
    `operator_salary` VARCHAR(45) NULL ,
    `office_name` VARCHAR(45) NULL ,
    `office_type` INT NULL ,
    PRIMARY KEY (`operator_id`) )';
    
    $this->create_sql['dim_customer'] = 
    'CREATE  TABLE IF NOT EXISTS `dwarehouse`.`dim_customer` (
    `customer_id` INT NOT NULL ,
    `customer_name` VARCHAR(45) NULL ,
    `customer_surname` VARCHAR(45) NULL ,
    `customer_age` INT NULL ,
    `customer_gender` TINYINT NULL ,
    PRIMARY KEY (`customer_id`) )';
  
    $this->create_sql['dim_date'] = 
    'CREATE  TABLE IF NOT EXISTS `dwarehouse`.`dim_date` (
    `date_id` INT NOT NULL ,
    `year` INT NULL ,
    `month` INT NULL ,
    `day` INT NULL ,
    PRIMARY KEY (`date_id`) )';
    
    $this->create_sql['holiday_detail_fact_table'] = 
    'CREATE  TABLE IF NOT EXISTS `dwarehouse`.`holiday_detail_fact_table` (
    `total_price` INT NULL ,
    `nights` INT NULL ,
    `persons` INT NULL ,
    `hotel_id` INT NOT NULL ,
    `manager_id` INT NOT NULL ,
    `hotel_location_id` INT NOT NULL ,
    `operator_location_id` INT NOT NULL ,
    `customer_location_id` INT NOT NULL ,
    `operator_id` INT NOT NULL ,
    `customer_id` INT NOT NULL ,
    `date_from_id` INT NOT NULL ,
    `date_to_id` INT NOT NULL ,
    INDEX `fk_holiday_detail_fact_table_manager_bridge1` (`manager_id` ASC) ,
    INDEX `fk_holiday_detail_fact_table_dim_location1` (`hotel_location_id` ASC) ,
    INDEX `fk_holiday_detail_fact_table_dim_location2` (`operator_location_id` ASC) ,
    INDEX `fk_holiday_detail_fact_table_dim_operator1` (`operator_id` ASC) ,
    INDEX `fk_holiday_detail_fact_table_dim_customer1` (`customer_id` ASC) ,
    INDEX `fk_holiday_detail_fact_table_dim_location3` (`customer_location_id` ASC) ,
    INDEX `fk_holiday_detail_fact_table_dim_date1` (`date_from_id` ASC) ,
    INDEX `fk_holiday_detail_fact_table_dim_date2` (`date_to_id` ASC) ,
    CONSTRAINT `fk_holiday_detail_fact_table_dim_hotel`
      FOREIGN KEY (`hotel_id` )
      REFERENCES `dwarehouse`.`dim_hotel` (`hotel_id` )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
    CONSTRAINT `fk_holiday_detail_fact_table_manager_bridge1`
      FOREIGN KEY (`manager_id` )
      REFERENCES `dwarehouse`.`manager_bridge` (`manager_id` )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
    CONSTRAINT `fk_holiday_detail_fact_table_dim_location1`
      FOREIGN KEY (`hotel_location_id` )
      REFERENCES `dwarehouse`.`dim_location` (`location_id` )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
    CONSTRAINT `fk_holiday_detail_fact_table_dim_location2`
      FOREIGN KEY (`operator_location_id` )
      REFERENCES `dwarehouse`.`dim_location` (`location_id` )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
    CONSTRAINT `fk_holiday_detail_fact_table_dim_operator1`
      FOREIGN KEY (`operator_id` )
      REFERENCES `dwarehouse`.`dim_operator` (`operator_id` )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
    CONSTRAINT `fk_holiday_detail_fact_table_dim_customer1`
      FOREIGN KEY (`customer_id` )
      REFERENCES `dwarehouse`.`dim_customer` (`customer_id` )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
    CONSTRAINT `fk_holiday_detail_fact_table_dim_location3`
      FOREIGN KEY (`customer_location_id` )
      REFERENCES `dwarehouse`.`dim_location` (`location_id` )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
    CONSTRAINT `fk_holiday_detail_fact_table_dim_date1`
      FOREIGN KEY (`date_from_id` )
      REFERENCES `dwarehouse`.`dim_date` (`date_id` )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
    CONSTRAINT `fk_holiday_detail_fact_table_dim_date2`
      FOREIGN KEY (`date_to_id` )
      REFERENCES `dwarehouse`.`dim_date` (`date_id` )
      ON DELETE NO ACTION
      ON UPDATE NO ACTION)';
    
    
    
    
    
    $this->clearTables($this->create_sql, 'Clear the warehouse tables' );
    
    $this->printer->output('<h3>Create the warehouse tables</h3>');
    foreach ($this->create_sql as $create_query)
    {
       $this->mw_import->executeQuery( $create_query );
    }
   
    
  }
  
  
  
  public function cleanUp(){
    
    $this->mw_import->closeConnection();
    return 'cleanUp';
  }
  
  
  
}
