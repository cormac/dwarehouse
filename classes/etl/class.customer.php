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

    $insert_queries['temp_viaggi'] = sprintf(
    'INSERT INTO merge_customers ( customerID, CName, CSurname, Age, Gender, CountryID, origin )
     SELECT customerID, Cname, Csurname, Age, IF(Gender=\'male\', 0, 1), mt_country.CountryID, %d AS origin FROM vi_customers 
     LEFT JOIN mt_country ON mt_country.CountryName = vi_customers.CountryID
     WHERE Age > 17 AND Age < 121
    ;', VI );
    $this->printer->output('<h2>Customers</h2>');
    
    for ($i = 20; $i < 23; $i++)
    {
      $insert_queries['viaggi_' . $i] = sprintf(
    'INSERT INTO merge_customers ( customerID, CName, CSurname, Age, Gender, CountryID, origin )
    VALUES (%d, \'dummy\', \'dummy\', 30, 0, 1, %d)', $i, VI ); 
      
    }
    
    
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
    $update_to_merge_table_query = sprintf(
    'INSERT INTO merge_customers 
    (customerID, Cname, Csurname, Age, Gender, CountryID, origin ) 
    SELECT customerID, Cname, Csurname, Age, Gender, CountryID, %d AS origin FROM MT_Customers;', MT );
    
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
    $insert_queries['gb_customer'] = sprintf(
    'INSERT INTO merge_customers ( customerID, CName, CSurname, Age, Gender, CountryID, origin )
     SELECT customerID, Cname, Csurname, Age,  IF(Gender = \'male\', 0, 1 ), mt_country.CountryID, %d AS origin FROM gb_customers 
     LEFT JOIN mt_country ON mt_country.CountryName = gb_customers.CountryID
     WHERE Age > 18 AND Age < 120;', GB );
    

    for ($i = 20; $i < 23; $i++)
    {
      $insert_queries['viaggi_' . $i] = sprintf(
    'INSERT INTO merge_customers ( customerID, CName, CSurname, Age, Gender, CountryID, origin )
    VALUES (%d, \'dummy\', \'dummy\', 30, 0, 1, %d)', $i, GB ); 
      
    }
    
    $this->printer->output('<h3>insert to merge</h3>'); 
    foreach ($insert_queries as $insert_query){
    
       $this->mw_import->executeQuery( $insert_query );
       
    }
  }
  
  
}
