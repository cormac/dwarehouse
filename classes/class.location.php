<?php


class LocationTransformation extends Transformer{
  
  /**
   *
   */
  public function importContinentTableContents(){
    $this->printer->output( '<h1>Location Translation</h1>' );
    $this->printer->output('<h2>Import Continent table</h2>');
    $query = 'INSERT INTO merge_continent (ContinentID, ContinentName) 
              SELECT ContinentID, ContinentName FROM mt_continent;';
              

    $this->mw_import->executeQuery($query);
  }
  
  function importCountryTableContents(){
    $query = 'INSERT INTO merge_country (CountryID)';
  }

}

