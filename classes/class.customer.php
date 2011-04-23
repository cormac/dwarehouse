<?php


class CustomerTransformation{
  private $mw_import;
  private $tables;
  private $create_sql;
  private $temp_table_sql;
  private $printer;
  
  public function __construct($verbose = true){
    $this->mw_import = new MysqlWrapper('dw_import');
    $this->tables = array('merge_customers');
    $this->printer = new Printer($verbose);
    $this->printer->output( '<h1>Customer Translation</h2>' );
  }
  
  
  /**
   *
   */
  public function separateErrors(){
    $this->printer->output( '<h2>write errors to their own table</h2>' );
    $base_query = '
    INSERT INTO ETLErrors( CustomerID, Age, originatingTable) 
    SELECT CustomerID, Age,  (\'%s\') as originatingTable FROM vi_customers 
    WHERE Age < %d OR Age > %d';
    $error_queries['vi_customers'] = sprintf( $base_query, 'vi_customers', 18, 120 );
    $error_queries['gb_customers'] = sprintf( $base_query, 'gb_customers', 18, 120 );
    $error_queries['mt_customers'] = sprintf( $base_query, 'mt_customers', 18, 120 );
    
    foreach ($error_queries as $error_query){
      $this->printer->output ($error_query . '<br/>');
      $this->mw_import->executeQuery( $error_query );
    }
  }
  
  public function writeViaggiCustomerToEtl(){
    
    $this->printer->output('<h2>Viaggi</h2>');
    
    
    //IMPORT DATA INTO TEMP TABLE

    $insert_queries['temp_viaggi'] = 'INSERT INTO temp_viaggi_customers ( customerID, CName, CSurname, Age, Gender, CountryID )
     SELECT customerID, Cname, Csurname, Age, Gender, CountryID FROM vi_customers WHERE Age > 17 AND Age < 121
    ;';
    $this->printer->output('<h2>Customers</h2>');
    
    
    
    $this->printer->output('<h3>insert temporary data</h3>'); 
    foreach ($insert_queries as $insert_query){
    
       $this->mw_import->executeQuery( $insert_query );
       $this->printer->output($insert_query . '<br/>');
       
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
    
    
    
    $this->printer->output('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
    
       $this->mw_import->executeQuery( $update_query );
       $this->printer->output($update_query . '<br/>');
       
    }
    
    //WRITE TO MERGE TABLE
    
    $update_to_merge_table_query = 
    'INSERT INTO merge_customers (customerID, Cname, Csurname, Age, Gender, CountryID) 
    SELECT customerID, Cname, Csurname, Age, gender_target, country_id_target FROM temp_viaggi_customers;';
    
    $this->printer->output('<h3>write to merge viaggi</h3>'); 
    $this->printer->output($update_to_merge_table_query); 
    
    $this->mw_import->executeQuery( $update_to_merge_table_query );
  }
    
    
  
  
  /**
   *
   */
  public function writeMTCustomerToEtl(){
    $this->printer->output('<h2>My Travel</h2>');
    $update_to_merge_table_query = 
    'INSERT INTO merge_customers 
    (customerID, Cname, Csurname, Age, Gender, CountryID) 
    SELECT customerID, Cname, Csurname, Age, Gender, CountryID FROM MT_Customers;';
    
    $this->printer->output('<h3>write to merge mt</h3>'); 
    $this->printer->output($update_to_merge_table_query); 
    $this->mw_import->executeQuery( $update_to_merge_table_query );
  }
  
  
  
   
  /**
   *
   */
  public function writeGBCustomerToEtl(){
    
    $this->printer->output('<h2>GoodBye.com</h2>');
    // WRITE TO TEMP TABLE
    
    $this->printer->output('<h2>Customers</h2>');
    $insert_queries['gb_customer'] = 'INSERT INTO temp_gb_customers ( customerID, CName, CSurname, Age, Gender, CountryID )
     SELECT customerID, Cname, Csurname, Age, Gender, CountryID FROM gb_customers WHERE Age > 18 AND Age < 120
    ;';
    

    
    
    $this->printer->output('<h3>insert temporary data</h3>'); 
    foreach ($insert_queries as $insert_query){
    
       $this->mw_import->executeQuery( $insert_query );
       $this->printer->output($insert_query . '<br/>');
       
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
    
    $this->printer->output('<h3>update the data</h3>'); 
    foreach ($update_queries as $update_query){
    
       $this->mw_import->executeQuery( $update_query );
       $this->printer->output($update_query . '<br/>');
       
    }
    
    //WRITE TO THE MERGE TABLE
    $update_to_merge_table_query = 
    'INSERT INTO merge_customers (customerID, Cname, Csurname, Age, Gender, CountryID) 
    SELECT customerID, Cname, Csurname, Age, gender_target, country_id_target FROM temp_gb_customers;';
    
    $this->printer->output('<h3>write to merge gb</h3>'); 
    $this->printer->output($update_to_merge_table_query); 
    
    $this->mw_import->executeQuery( $update_to_merge_table_query );
  }
  
  /**
   *
   */
    public function cleanUp(){
    
    $this->mw_import->closeConnection();
    return 'cleanUp';
  }
}
