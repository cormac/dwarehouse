<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 23/04/11
Description   : Perform transformations on the holiday details tables
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

class HolidayDetailsTransformation extends Transformer{
  /**
   *
   */
  public function writeHolidayDetailsToEtl(){
    $this->printer->output( '<h1>Holiday Details Translation</h1>' );
    
    //WRITE TO MERGE TABLE
    $this->writeGBHolidayDetailsToEtl();
    $this->writeMTHolidayDetailsToEtl();
    $this->writeVIHolidayDetailsToEtl();
  }
  
  /**
   *
   */
  private function writeGBHolidayDetailsToEtl(){
    $this->printer->output('<h2>GoodBye.com</h2>');
    $query = sprintf(
    'INSERT INTO merge_holiday_details( HolidayDetailsID, HolidayID, Nigths, HotelID, Price_per_Night, Price_Extra, Persons, origin )
     SELECT HolidayDetailsID, HolidayID, Nigths, HotelID, Price_per_Night, 0 AS Price_Extra, Persons, %d as origin FROM gb_holidaydetails;', GB );
    $this->mw_import->executeQuery( $query );
  }
  
  /**
   *
   */
  private function writeMTHolidayDetailsToEtl(){
    $this->printer->output('<h2>MyTravel.com</h2>');
    $query = sprintf(
    'INSERT INTO merge_holiday_details( HolidayDetailsID, HolidayID, Nigths, HotelID, Price_per_Night, Price_Extra, Persons, origin )
     SELECT HolidayDetailsID, HolidayID, Nigths, HotelID, Price_per_Night, Price_Extra, Persons, %d as origin FROM mt_holidaydetails;', MT );
    $this->mw_import->executeQuery( $query );
  }
  
  /**
   *
   */
  private function writeVIHolidayDetailsToEtl(){
    $this->printer->output('<h2>Viaggi</h2>');
    $query = sprintf(
    'INSERT INTO merge_holiday_details( HolidayDetailsID, HolidayID, Nigths, HotelID, Price_per_Night, Price_Extra, Persons, origin )
     SELECT HolidayDetailsID, HolidayID, Nigths, HotelID, Price_per_Night, Price_Extra, Persons, %d as origin FROM vi_holidaydetails;', VI );
    $this->mw_import->executeQuery( $query );
  }
  
  
}
  
