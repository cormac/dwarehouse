<?php

$vars = array( 'ct', 'cust_import', 'hotel_import' );

include( 'krumo/class.krumo.php' );
include( 'classes/class.mysqlwrapper.php' );
include( 'classes/class.createtables.php' );
include( 'classes/class.customer.php' );
include( 'classes/class.hotel.php' );




$ct = new CreateTables();


$cust_import = new CustomerTransformation();

$hotel_import = new HotelTransformation();

$ct->buildCreateStatements();
$ct->createTempTables();
$ct->createErrorTable();
$cust_import->separateErrors();



print( '<h1>Customer Translation</h2>' );
print('<h2>Viaggi</h2>');
$cust_import->writeViaggiCustomerToEtl();


print('<h2>My Travel</h2>');

$cust_import->writeMTCustomerToEtl();


print('<h2>GoodBte.com</h2>');
$cust_import->writeGBCustomerToEtl();


print( '<h1>Hotel Translation</h2>' );
print('<h2>Viaggi</h2>');
krumo('hello');

$hotel_import->writeViaggiHotelToEtl();



print('<h2>My Travel</h2>');
$hotel_import->writeMTHotelToEtl();
print('<h2>GoodBye.com</h2>');
$hotel_import->writeGBHotelToEtl();





foreach( $vars as $var ){
  try{
     $$var->cleanUp();
  }catch(Exception $e){
    krumo( $e );
  }
}
/**/
