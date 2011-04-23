<?php


class LocationTransformation{
  
  private $mw_import;
  private $printer;
  
  function __construct ($verbose = true ){
    

    $this->mw_import = new MysqlWrapper('dw_import');
    $this->printer = new Printer($verbose);
    $this->printer->output( '<h1>Location Translation</h2>' );
  }
  
  /**
   *
   */
  public function importContinentTableContents(){
    $this->printer->output('<h2>Import Continent table</h2>');
    $query = 'INSERT INTO merge_continent (ContinentID, ContinentName) 
              SELECT ContinentID, ContinentName FROM mt_continent;';
              
    $this->printer->output( $query );
    $this->mw_import->executeQuery($query);
  }
  
  function importCountryTableContents(){
    $query = 'INSERT INTO merge_country (CountryID)';
  }

}

