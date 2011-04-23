<?php
include( 'krumo/class.krumo.php' );

$vars = array( 'ct', 'cust_import', 'hotel_import',  );
$classes = array( 'mysqlwrapper', 'createtables' , 'customer' , 'office', 'hotel', 'location', 'printer'  );




foreach ($classes as $class){
  $file = 'classes/class.' . $class . '.php';
  require ($file);
//  include( 'classes/class.' . $class . '.php' );
}

$ct = new CreateTables(false);
$cust_import = new CustomerTransformation();
$hotel_import = new HotelTransformation();
$location_import = new LocationTransformation();
$office_import = new OfficeTransformation();



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



$hotel_import->writeViaggiHotelToEtl();

$hotel_import->writeMTHotelToEtl();

$hotel_import->writeGBHotelToEtl();


/*****************************************************************************************************************

  OFFICE IMPORT

*****************************************************************************************************************/


$office_import->writeGBOfficeToEtl();
$office_import->writeMTOfficeToEtl();
$office_import->writeViaggiOfficeToEtl();




/*****************************************************************************************************************

  LOCATION IMPORT

*****************************************************************************************************************/


$location_import->importContinentTableContents();







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
/**/
