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
    $this->printer->output( '<h1>Office Translation</h1>' );
    
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
    $query = 
    'INSERT INTO merge_holiday( HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID)
     SELECT HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID FROM gb_holiday;';
    $this->mw_import->executeQuery( $query );
  }
  
  /**
   *
   */
  private function writeMTHolidayToEtl(){
    $this->printer->output('<h2>MyTravel.com</h2>');
    $query = 
    'INSERT INTO merge_holiday( HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID)
     SELECT HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID FROM mt_holiday;';
    $this->mw_import->executeQuery( $query );
  }
  
  /**
   *
   */
  private function writeVIHolidayToEtl(){
    $this->printer->output('<h2>Viaggi</h2>');
    $query = 
    'INSERT INTO merge_holiday( HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID)
     SELECT HolidayID, CustomerID, Date_From, Date_To, Totalprice, OperatorID FROM vi_holiday;';
    $this->mw_import->executeQuery( $query );
  }
  
  
}
  
