<?php


class LocationTransformation extends Transformer{
  
  /**
   *
   */
  public function importLocationTableContents(){
    $this->printer->output( '<h1>Location Translation</h1>' );
    $this->printer->output('<h2>Import Continent table</h2>');
    $query = 
    'INSERT INTO merge_location (CityName, RegionName, CountryName, ContinentName, CityId, RegionId, CountryId, ContinentID)
     SELECT mt_city.CityName, mt_region.RegionName, mt_country.CountryName, mt_continent.ContinentName, 
            mt_city.CityId, mt_region.RegionId, mt_country.CountryId, mt_continent.ContinentID FROM mt_city  
     LEFT JOIN mt_region ON mt_city.RegionId  = mt_region.RegionID  
     LEFT JOIN mt_country ON mt_country.CountryId  = mt_region.CountryID  
     LEFT JOIN mt_continent ON mt_country.ContinentID = mt_continent.ContinentID;';
              

    $this->mw_import->executeQuery($query);
  }
  
}

