<?php


class CustomerTransformation{
  private $mw_import;
  private $tables;
  private $create_sql;
  private $temp_table_sql;
  
  
  public function __construct(){
    $this->mw_import = new MysqlWrapper('dw_import');
    $this->mw_import->openConnection();
    $this->tables = array('merge_customers');
  }
  
  
  /**
   *
   */
  public function separateErrors(){
    print( '<h2>write errors to their own table</h2>' );
    $base_query = '
    INSERT INTO ETLErrors( CustomerID, Age, originatingTable) 
    SELECT CustomerID, Age,  (\'%s\') as originatingTable FROM vi_customers 
    WHERE Age < %d OR Age > %d';
    $error_queries['vi_customers'] = sprintf( $base_query, 'vi_customers', 18, 120 );
    $error_queries['gb_customers'] = sprintf( $base_query, 'gb_customers', 18, 120 );
    $error_queries['mt_customers'] = sprintf( $base_query, 'mt_customers', 18, 120 );
    
    foreach ($error_queries as $error_query){
      print $error_query . '<br/>';
      $this->mw_import->executeQuery( $error_query );
    }
  }
  
  public function writeViaggiCustomerToEtl(){
    
    
    
    
    //IMPORT DATA INTO TEMP TABLE

    $insert_queries['temp_viaggi'] = 'INSERT INTO temp_viaggi_customers ( customerID, CName, CSurname, Age, Gender, CountryID )
     SELECT customerID, Cname, Csurname, Age, Gender, CountryID FROM vi_customers WHERE Age > 17 AND Age < 121
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
    $update_to_merge_table_query = 
    'INSERT INTO merge_customers 
    (customerID, Cname, Csurname, Age, Gender, CountryID) 
    SELECT customerID, Cname, Csurname, Age, Gender, CountryID FROM MT_Customers;';
    
    print('<h3>write to merge mt</h3>'); 
    print($update_to_merge_table_query); 
    $this->mw_import->executeQuery( $update_to_merge_table_query );
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
  
  /**
   *
   */
    public function cleanUp(){
    
    $this->mw_import->closeConnection();
    return 'cleanUp';
  }
}
