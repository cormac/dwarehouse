<?php

class CreateTables{

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
    $this->tables = array('merge_customers');
    $this->printer = new Printer($verbose);
  }
  
  /**
   *
   */
  public function createErrorTable(){
    $this->error_tables['ETLErrors'] = '
      CREATE TABLE ETLErrors (
        errorID int PRIMARY KEY NOT NULL AUTO_INCREMENT,
        customerID int,
        Age int,
        originatingTable varchar(12)
      );
    ';
    $this->clearTables($this->error_tables, 'Clear the error table' );
    $this->printer->output('<h3>Create the error tables</h3>');
    foreach ($this->error_tables as $create_query)
    {
       $this->mw_import->executeQuery( $create_query );

    }
    
    
  }
  
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
  
    $this->printer->output( '<h1>Staging Tables</h1>' );
    $cid = 'S';
    $this->create_sql['merge_customers'] = sprintf( 
    'CREATE TABLE merge_customers (
    %sCustomerID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    origin int,
    customerID int,
    Cname varchar(50),
    Csurname varchar(50),
    Age int,
    Gender int,
    CountryID int);' 
    , $cid);
    
    $this->create_sql['merge_hotel'] = sprintf(
      'CREATE TABLE merge_hotel (
      %sHotelID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
      origin int,
      HotelID int,
      Hname varchar(50),
      HAddress varchar(200),
      Category int,
      Telephone varchar(20),
      NRoom int,
      CityID int,
      ManagerID varchar(10)
      );'
      , $cid
    );
    
    $this->create_sql['merge_location'] = sprintf(
      'CREATE TABLE merge_location (
      LocationID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
      CityName varchar(50),
      RegionName varchar(50),
      CountryName varchar(50),
      ContinentName varchar(50),
      CityId int,
      RegionID int,
      CountryID int,
      ContinentID int
      );'
      , $cid
    );
    
    
    $this->create_sql['merge_hotel_manager'] = sprintf(
      'CREATE TABLE merge_hotel_manager (
        %sHotelManagerID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        ManagerID varchar(10),
        ManagerName varchar(50),
        ManagerSurname varchar(50),
        ResponsibleID varchar(10)     
      );'
      , $cid
    );
    
    $this->create_sql['merge_holiday'] = sprintf(
      'CREATE TABLE merge_holiday (
        %sHolidayID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        origin int,
        HolidayID int,
        CustomerID int,
        Date_From varchar(10),
        Date_To varchar(10),
        Totalprice float,
        OperatorID int
      );'
      , $cid
    );
    $this->create_sql['merge_holiday_details'] = sprintf(
      'CREATE TABLE merge_holiday_details (
        %sHolidayDetailsID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        HolidayDetailsID int,
        origin int,
        HolidayID int,
        Nigths int,
        HotelID int,
        Price_per_Night float,
        Price_Extra float,
        Persons int
      );'
      , $cid
    );
    
    $this->create_sql['merge_operators_offices'] = sprintf(
      'CREATE TABLE merge_operators_offices (
        %sOperatorsID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        origin int,
        operatorId int,
        Oname varchar(50),
        OSurname varchar(50),
        Salary float,
        OfficeName varchar(50), 
        CityID int, 
        OType int
      );'
      , $cid
    );
    
    
   
    
    
    
    $this->clearTables($this->create_sql, 'Clear the staging tables' );
    
    $this->printer->output('<h3>Create the staging tables</h3>');
    foreach ($this->create_sql as $create_query)
    {
       $this->mw_import->executeQuery( $create_query );
    }
   
    
  }
  
  
  
  
  public function createTempTables(){
    $this->printer->output('<h1> Create temporary Tables </h1>');
    
    
    $this->temp_table_sql['temp_viaggi_customers'] = sprintf( 
    'CREATE TABLE temp_viaggi_customers (
    customerID int,
    Cname varchar(50),
    Csurname varchar(50),
    Age int,
    Gender char,
    gender_target int,
    country_id_target int,
    CountryID varchar(50));' 
    );
    
    $this->temp_table_sql['temp_gb_customers'] = '
    CREATE TABLE temp_gb_customers (
      customerID int,
      Cname varchar(50),
      Csurname varchar(50),
      Age int,
      Gender varchar(10),
      gender_target int,
      country_id_target int,
      CountryID varchar(20)
    );
    ';
    
    $this->temp_table_sql['temp_gb_hotel'] = '
    CREATE TABLE temp_gb_hotel (
      HotelID int,
      Hname varchar(50),
      HAddress varchar(200),
      Category int,
      Telephone varchar(20),
      NRoom int,
      CityID int,
      ManagerID varchar(10),
      str_category varchar(8),
      int_mt_cityID int
      
    );
    ';
    $this->temp_table_sql['temp_viaggi_hotel'] = '
    CREATE TABLE temp_viaggi_hotel (
      HotelID int,
      Hname varchar(50),
      HAddress varchar(200),
      Category int,
      Telephone varchar(20),
      NRoom int,
      CityID int,
      ManagerID varchar(10),
      str_category varchar(8),
      int_mt_cityID int
    );
    ';
    
    $this->temp_table_sql['temp_gb_offices'] = '
      CREATE TABLE temp_gb_offices (
        OfficeId int,
        OfficeName varchar(50),
        OAddress varchar(50),
        CityID int,
        OType int,
        int_CityID int,
        int_mt_cityID int
      );';
    
    $this->temp_table_sql['temp_vi_offices'] ='
      CREATE TABLE temp_vi_offices (
        OfficeId int,
        OfficeName varchar(50),
        OAddress varchar(50),
        CityID int,
        OType int,
        int_CityID int,
        int_mt_cityID int
      );';
      
      
    
    
    $this->clearTables($this->temp_table_sql, 'Clear temp tables' );
    
    //CREATE THE TEMPORARY TABLES
    foreach ($this->temp_table_sql as $temp_query){
    
       $this->mw_import->executeQuery( $temp_query );
       
    }
    
    
  }
  
  
  
  
  
  
  public function cleanUp(){
    
    $this->mw_import->closeConnection();
    return 'cleanUp';
  }
  
  
  
}
