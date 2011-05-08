<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 01/05/11
Description   : This handles the transformation of all tables in the etl phase
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

$vars = array( 'ct', 'cust_import', 'hotel_import',  );
$etl_classes = array( 'transform', 'operators', 'createtables' , 'customer' ,  'hotel', 'location', 'holiday', 'holidaydetails' );


foreach ( $etl_classes as $class ){
  $file = 'classes/etl/class.' . $class . '.php';
  require( $file );
}

echo( '<h1>ETL Phase</h1>' );

$verbose                = false;
$ct                     = new CreateTables( $verbose );
$location_import        = new LocationTransformation( $verbose );
$cust_import            = new CustomerTransformation( $verbose );
$hotel_import           = new HotelTransformation( $verbose );


$operator_import        = new OperatorTransformation( $verbose );
$holiday_import         = new HolidayTransformation( $verbose );
$holiday_details_import = new HolidayDetailsTransformation( $verbose );


$ct->buildCreateStatements();
$ct->createTempTables();
$ct->createErrorTable();
$cust_import->separateErrors();


/*****************************************************************************************************************

  LOCATION IMPORT

*****************************************************************************************************************/

$location_import->importLocationTableContents();

/*****************************************************************************************************************

  CUSTOMER IMPORT

*****************************************************************************************************************/

$cust_import->writeViaggiCustomerToEtl();
$cust_import->writeMTCustomerToEtl();
$cust_import->writeGBCustomerToEtl();

/*****************************************************************************************************************

  HOTEL IMPORT

*****************************************************************************************************************/

$hotel_import->writeGBHotelToEtl();
$hotel_import->writeViaggiHotelToEtl();
$hotel_import->writeMTHotelToEtl();




/*****************************************************************************************************************

  HOLIDAY IMPORT

*****************************************************************************************************************/

$holiday_import->writeHolidaysToEtl( );


/*****************************************************************************************************************

  HOLIDAY DETAILS IMPORT

*****************************************************************************************************************/

$holiday_details_import->writeHolidayDetailsToEtl( );




/*****************************************************************************************************************

  OPERATOR IMPORT

*****************************************************************************************************************/

$operator_import->writeOperatorsToEtl( );





/*****************************************************************************************************************

  CLOSE DB CONNECTIONS

*****************************************************************************************************************/

foreach( $vars as $var ){
  try{
     $$var->cleanUp();
  }catch(Exception $e){
    krumo( $e );
  }
}

