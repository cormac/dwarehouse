<?php
include( 'krumo/class.krumo.php' );

$vars = array( 'ct', 'cust_import', 'hotel_import',  );
$classes = array( 'mysqlwrapper', 'transform', 'operators', 'createtables' , 'customer' , 'office', 'hotel', 'location', 'printer', 'holiday', 'holidaydetails' );

foreach ( $classes as $class ){
  $file = 'classes/class.' . $class . '.php';
  include( $file );

}


$verbose                = false;
$ct                     = new CreateTables( $verbose );
$cust_import            = new CustomerTransformation( $verbose );
$hotel_import           = new HotelTransformation( $verbose );
$location_import        = new LocationTransformation( $verbose );
$office_import          = new OfficeTransformation( $verbose );
$operator_import        = new OperatorTransformation( $verbose );
$holiday_import         = new HolidayTransformation( $verbose );
$holiday_details_import = new HolidayDetailsTransformation( true );


$ct->buildCreateStatements();
$ct->createTempTables();
$ct->createErrorTable();
$cust_import->separateErrors();


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

  OFFICE IMPORT

*****************************************************************************************************************/

$office_import->writeGBOfficeToEtl();
$office_import->writeMTOfficeToEtl();
$office_import->writeViaggiOfficeToEtl();

/*****************************************************************************************************************

  LOCATION IMPORT

*****************************************************************************************************************/

$location_import->importLocationTableContents();

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

