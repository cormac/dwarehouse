<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 23/04/11
Description   : Perform transformations on the holiday tables
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

class HolidayTransformation extends Transformer{
  /**
   *
   */
  public function writeHolidaysToEtl(){
    $this->printer->output( '<h1>Holiday Translation</h1>' );
    
    //WRITE TO MERGE TABLE
    $this->writeGBHolidayToEtl();
    $this->writeMTHolidayToEtl();
    $this->writeVIHolidayToEtl();
  }
  
  /**
   *
   */
  private function writeGBHolidayToEtl(){
    $this->printer->output('<h2>GoodBye.com</h2>');
    $query = sprintf(
    'INSERT INTO merge_holiday( HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID, origin)
     SELECT HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID, %d as origin FROM gb_holiday;', GB );
    $this->mw_import->executeQuery( $query );
  }
  
  /**
   *
   */
  private function writeMTHolidayToEtl(){
    $this->printer->output('<h2>MyTravel.com</h2>');
    $query = sprintf(
    'INSERT INTO merge_holiday( HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID, origin)
     SELECT HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID, %d as origin FROM mt_holiday;', MT );
    $this->mw_import->executeQuery( $query );
  }
  
  /**
   *
   */
  private function writeVIHolidayToEtl(){
    $this->printer->output('<h2>Viaggi</h2>');
    $query = sprintf(
    'INSERT INTO merge_holiday( HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID, origin)
     SELECT HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID, %d as origin FROM vi_holiday;', VI );
    $this->mw_import->executeQuery( $query );
  }
  
  
}
  
