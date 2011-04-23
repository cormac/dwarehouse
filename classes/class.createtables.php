<?php

class CreateTables{

  private $mw_import;
  private $tables;
  private $create_sql;
  private $temp_table_sql;
  private $error_tables;
  
  public function __construct(){
    $this->mw_import = new MysqlWrapper('dw_import');
    $this->mw_import->openConnection();
    //$this->mw_import = new MysqlWrapper('dw_etl');
    //$this->mw_import->openConnection();
    $this->tables = array('merge_customers');
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
    print('<h3>Create the error tables</h3>');
    foreach ($this->error_tables as $create_query)
    {
       $this->mw_import->executeQuery( $create_query );
       print($create_query . '<br/>');
    }
    
    
  }
  
  private function clearTables($tables, $label){
    print( '<h3>' . $label . '</h3>' );
    foreach ($tables as $key => $statement){
      $clear_statement = sprintf('DROP TABLE IF EXISTS %s;', $key);
      print( $clear_statement . '<br/>');
      $this->mw_import->executeQuery( $clear_statement );
    }
    
  }
  
  /**
   *
   */
  /*****************************************************************************************************************
  
    CREATE TABLES
  
  *****************************************************************************************************************/
  public function buildCreateStatements(){
  
    print( '<h1>Staging Tables</h1>' );
    $cid = 'S';
    $this->create_sql['merge_customers'] = sprintf( 
    'CREATE TABLE merge_customers (
    %sCustomerID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
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
    
    $this->create_sql['merge_city'] = sprintf(
      'CREATE TABLE merge_city (
      %sCityID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
      CityId int,
      CityName varchar(50),
      RegionID int
      );'
      , $cid
    );
    
    $this->create_sql['merge_region'] = sprintf(
      'CREATE TABLE merge_region (
        %sRegionID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        RegionId int,
        RegionName varchar(50),
        CountryID int
      );'
      , $cid
    );
    
    $this->create_sql['merge_country'] = sprintf(
      'CREATE TABLE merge_country (
        %sCountryID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        CountryId int,
        CountryName varchar(50),
        ContinentID int
      );'
      , $cid
    );
    
    $this->create_sql['merge_continent'] = sprintf(
      'CREATE TABLE merge_continent (
        %sContinentID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        ContinentID int,
        ContinentName varchar(50)
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
        HolidayID int,
        Nigths int,
        HotelID int,
        Price_per_Night float,
        Price_Extra float,
        Persons int
      );'
      , $cid
    );
    
    $this->create_sql['merge_operators'] = sprintf(
      'CREATE TABLE merge_operators (
        %sOperatorsID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        operatorId int,
        Oname varchar(50),
        OSurname varchar(50),
        Salary float,
        OfficeId int
      );'
      , $cid
    );
    
    $this->create_sql['merge_offices'] = sprintf(
      'CREATE TABLE merge_offices (
        %sOfficesID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
        OfficeId int,
        OfficeName varchar(50),
        OAddress varchar(50),
        CityID int,
        OType int
      );'
      , $cid
    );
   
    
    
    
    $this->clearTables($this->create_sql, 'Clear the staging tables' );
    
    print('<h3>Create the staging tables</h3>');
    foreach ($this->create_sql as $create_query)
    {
       $this->mw_import->executeQuery( $create_query );
       print($create_query . '<br/>');
    }
   
    
  }
  
  
  
  
  public function createTempTables(){
    print('<h1> Create temporary Tables </h1>');
    
    
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
      str_category varchar(8)
      
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
      str_category varchar(8)
      
    );
    ';
    
    
    
    $this->clearTables($this->temp_table_sql, 'Clear temp tables' );
    
    //CREATE THE TEMPORARY TABLES
    foreach ($this->temp_table_sql as $temp_query){
    
       $this->mw_import->executeQuery( $temp_query );
       print($temp_query . '<br/>');
       
    }
    
  }
  
  
  
  
  
  
  public function cleanUp(){
    
    $this->mw_import->closeConnection();
    return 'cleanUp';
  }
  
  
  
}
