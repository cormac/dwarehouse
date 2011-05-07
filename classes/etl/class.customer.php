<?php


class CustomerTransformation extends Transformer{

  private $tables;
  private $create_sql;
  private $temp_table_sql;
  
  /**
   *
   */
  public function separateErrors(){
    $this->printer->output( '<h2>write errors to their own table</h2>' );
    $base_query = '
    INSERT INTO ETLErrors( CustomerID, Age, originatingTable) 
    SELECT CustomerID, Age,  (\'%s\') as originatingTable FROM %S 
    WHERE Age < %d OR Age > %d';
    $error_queries['vi_customers'] = sprintf( $base_query, 'vi_customers', 'vi_customers', 18, 120 );
    $error_queries['gb_customers'] = sprintf( $base_query, 'gb_customers', 'gb_customers', 18, 120 );
    $error_queries['mt_customers'] = sprintf( $base_query, 'mt_customers', 'mt_customers', 18, 120 );
    
    foreach ($error_queries as $error_query){

      $this->mw_import->executeQuery( $error_query );
    }
  }
  
  public function writeViaggiCustomerToEtl(){
    $this->printer->output( '<h1>Customer Translation</h2>' );
    $this->printer->output('<h2>Viaggi</h2>');
    
    
    //IMPORT DATA INTO TEMP TABLE

    $insert_queries['temp_viaggi'] = 'INSERT INTO merge_customers ( customerID, CName, CSurname, Age, Gender, CountryID )
     SELECT customerID, Cname, Csurname, Age, IF(Gender=\'male\', 0, 1), mt_country.CountryID FROM vi_customers 
     JOIN mt_country ON mt_country.CountryName = vi_customers.CountryID
     WHERE Age > 17 AND Age < 121
    ;';
    $this->printer->output('<h2>Customers</h2>');
    
    
    
    $this->printer->output('<h3>insert temporary data</h3>'); 
    foreach ($insert_queries as $insert_query){
    
       $this->mw_import->executeQuery( $insert_query );
       
    }
    
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
    
    $this->printer->output( '<h3>write to merge mt</h3>' ); 

    $this->mw_import->executeQuery( $update_to_merge_table_query );
  }
  
  
  
   
  /**
   *
   */
  public function writeGBCustomerToEtl(){
    
    $this->printer->output('<h2>GoodBye.com</h2>');
    // WRITE TO TEMP TABLE
    
    $this->printer->output('<h2>Customers</h2>');
    $insert_queries['gb_customer'] = 'INSERT INTO merge_customers ( customerID, CName, CSurname, Age, Gender, CountryID  )
     SELECT customerID, Cname, Csurname, Age,  IF(Gender = \'male\', 0, 1 ), mt_country.CountryID FROM gb_customers 
     JOIN mt_country ON mt_country.CountryName = gb_customers.CountryID
     WHERE Age > 18 AND Age < 120;';
    

    
    
    $this->printer->output('<h3>insert to merge</h3>'); 
    foreach ($insert_queries as $insert_query){
    
       $this->mw_import->executeQuery( $insert_query );
       
    }
  }
  
  
}
