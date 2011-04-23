<?php

class CreateTables{

  private $mw_import;
  private $tables;
  private $create_sql;
  private $temp_table_sql;
  
  
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
    
    $this->temp_table_sql['temp_viaggi_hotel'] = '
    CREATE TABLE temp_gb_hotel (
      HotelID int,
      Hname varchar(50),
      HAddress varchar(200),
      Category int,
      Telephone varchar(20),
      NRoom int,
      CityID int,
      ManagerID varchar(10),
      str_HotelID varchar(10),
      str_category varchar(8),
      
    );
    ';
    
    
    
    $this->clearTables($this->temp_table_sql, 'Clear temp tables' );
    
    //CREATE THE TEMPORARY TABLES
    foreach ($this->temp_table_sql as $temp_query){
    
       $this->mw_import->executeQuery( $temp_query );
       print($temp_query . '<br/>');
       
    }
    
  }
  
  /*****************************************************************************************************************
  
    WRITE DATA TO CUSTOMER MERGE TABLES
  
  *****************************************************************************************************************/
  
   /**
   *
   */
  public function writeViaggiCustomerToEtl(){
    
    
    
    
    //IMPORT DATA INTO TEMP TABLE

    $insert_queries['temp_viaggi'] = 'INSERT INTO temp_viaggi_customers ( customerID, CName, CSurname, Age, Gender, CountryID )
     SELECT customerID, Cname, Csurname, Age, Gender, CountryID FROM vi_customers WHERE Age > 18 AND Age < 120
    ;';
    print('<h2>Customers</h2>');
    
    
    
    print('<h3>insert temporary data</h3>'); 
    foreach ($insert_queries as $insert_query){
    
       $this->mw_import->executeQuery( $insert_query );
       print($insert_query . '<br/>');
       
    }
    
    // UPDATE THE TEMP TABLE
    
    
    $update_queries['temp_viaggi_0'] = 
      'UPDATE temp_viaggi_customers SET gender_target = 0 WHERE gender = \'M\';';
    
    $update_queries['temp_viaggi_1'] = 
      'UPDATE temp_viaggi_customers SET gender_target = 1 WHERE gender = \'F\';';
    
    
    $select_query = "SELECT DISTINCT CountryID FROM temp_viaggi_customers";
    $this->mw_import->executeQuery( $select_query );
    
    while($row = mysql_fetch_object( $this->mw_import->__get('result') ) ){
      $country_ids[] = $row->CountryID; 
    }

    foreach($country_ids as $country_id){
      $select_query = sprintf( "SELECT CountryID FROM mt_country WHERE CountryName='%s'", $country_id );
      //krumo( $select_query );
      $this->mw_import->executeQuery( $select_query );
      
      $row = mysql_fetch_object( $this->mw_import->__get('result') );
      
      $update_queries[] = sprintf('UPDATE temp_viaggi_customers SET country_id_target=%d WHERE CountryID=\'%s\'', $row->CountryID, $country_id); 
    }
    
    
    
    
    
    print('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
    
       $this->mw_import->executeQuery( $update_query );
       print($update_query . '<br/>');
       
    }
    
    //WRITE TO MERGE TABLE
    
    $update_to_merge_table_query = 
    'INSERT INTO merge_customers (customerID, Cname, Csurname, Age, Gender, CountryID) 
    SELECT customerID, Cname, Csurname, Age, gender_target, country_id_target FROM temp_viaggi_customers;';
    
    print('<h3>write to merge viaggi</h3>'); 
    print($update_to_merge_table_query); 
    
    $this->mw_import->executeQuery( $update_to_merge_table_query );
    
  }
  
  
  /**
   *
   */
  public function writeMTCustomerToEtl(){
    print('<h2>Customers</h2>');
    $update_to_merge_table_query = 
    'INSERT INTO merge_customers 
    (customerID, Cname, Csurname, Age, Gender, CountryID) 
    SELECT customerID, Cname, Csurname, Age, Gender, CountryID FROM MT_Customers;';
    
    print('<h3>write to merge mt</h3>'); 
    print($update_to_merge_table_query); 
    $this->mw_import->executeQuery( $update_to_merge_table_query );
  }
  
  
  
  public function cleanUp(){
    
    $this->mw_import->closeConnection();
  }
  
  /**
   *
   */
  public function writeGBCustomerToEtl(){
    
    
    // WRITE TO TEMP TABLE
    
    print('<h2>Customers</h2>');
    $insert_queries['gb_customer'] = 'INSERT INTO temp_gb_customers ( customerID, CName, CSurname, Age, Gender, CountryID )
     SELECT customerID, Cname, Csurname, Age, Gender, CountryID FROM gb_customers WHERE Age > 18 AND Age < 120
    ;';
    

    
    
    print('<h3>insert temporary data</h3>'); 
    foreach ($insert_queries as $insert_query){
    
       $this->mw_import->executeQuery( $insert_query );
       print($insert_query . '<br/>');
       
    }
    //REPLACE MALE AND FEMALE
    $update_queries['temp_gb_0'] = 
      'UPDATE temp_gb_customers SET gender_target = 0 WHERE gender = \'male\';';
    
    $update_queries['temp_gb_1'] = 
      'UPDATE temp_gb_customers SET gender_target = 1 WHERE gender = \'female\';';
      
    
    
    
    //REPLACE COUNTRIES
    $select_query = "SELECT DISTINCT CountryID FROM temp_gb_customers";
    $this->mw_import->executeQuery( $select_query );
    
    while($row = mysql_fetch_object( $this->mw_import->__get('result') ) ){
      $country_ids[] = $row->CountryID; 
    }

    foreach($country_ids as $country_id){
      $select_query = sprintf( "SELECT CountryID FROM mt_country WHERE CountryName='%s'", $country_id );
      
      $this->mw_import->executeQuery( $select_query );
      
      $row = mysql_fetch_object( $this->mw_import->__get('result') );
      
      $update_queries[] = sprintf('UPDATE temp_gb_customers SET country_id_target=%d WHERE CountryID=\'%s\'', $row->CountryID, $country_id); 
    }
    
    print('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
    
       $this->mw_import->executeQuery( $update_query );
       print($update_query . '<br/>');
       
    }
    
    //WRITE TO THE MERGE TABLE
    $update_to_merge_table_query = 
    'INSERT INTO merge_customers (customerID, Cname, Csurname, Age, Gender, CountryID) 
    SELECT customerID, Cname, Csurname, Age, gender_target, country_id_target FROM temp_gb_customers;';
    
    print('<h3>write to merge gb</h3>'); 
    print($update_to_merge_table_query); 
    
    $this->mw_import->executeQuery( $update_to_merge_table_query );
  }
  
}
