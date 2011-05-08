<?php


class LocationTransformation extends Transformer{
  
  /**
   *
   */
  public function importLocationTableContents(){
    $this->addCities();
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
  
  public function addCities(){
    $this->printer->output('<h3>Here I added extra cities into the mt_city table to deal with values 
    contained in the other tables that were missing from my travel. This would probably be better done in the etl phase </h3>');
    $query = "INSERT INTO mt_city (CityID, CityName, RegionID)
      VALUES (43, 'Innsbruck', 37)
      INSERT INTO mt_city (CityID, CityName, RegionID)
      VALUES (44, 'Salzburg', 38)
      INSERT INTO mt_city (CityID, CityName, RegionID)
      VALUES (45, 'Wien', 39)
      INSERT INTO mt_city (CityID, CityName, RegionID)
      VALUES (46, 'Lienz', 40)
      INSERT INTO mt_city (CityID, CityName, RegionID)
      VALUES (47, 'Lisbon', 41)
      INSERT INTO mt_city (CityID, CityName, RegionID)
      VALUES (48, 'Porto', 42)
      INSERT INTO mt_city (CityID, CityName, RegionID)
      VALUES (49, 'Madrid', 43)
      INSERT INTO mt_city (CityID, CityName, RegionID)
      VALUES (50, 'Barcelona', 44);";
      $this->mw_import->executeQuery($query);
  }
  
  
}

