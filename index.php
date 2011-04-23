<?php

include( 'classes/mysqlwrapper.php' );
include( 'classes/class.createtables.php' );
include( 'classes/class.customer.php' );

include( 'krumo/class.krumo.php' );

$ct = new CreateTables();


$cust_import = new CustomerTransformation();


$ct->buildCreateStatements();
$ct->createTempTables();

print('<h1>Viaggi</h1>');
$cust_import->writeViaggiCustomerToEtl();
print('<h1>My Travel</h1>');
$cust_import->writeMTCustomerToEtl();
print('<h1>GoodBte.com</h1>');
$cust_import->writeGBCustomerToEtl();
$ct->cleanUp();

