<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 01/05/2011
Description   : Transform the data from the etl tables to the star schema
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

$dw_classes = array( 'createdwtables', 'hierarchy' );

foreach ( $dw_classes as $class ){
  $file = 'classes/dw/class.' . $class . '.php';
  include( $file );
}



$verbose          = false;
$dw_table_creator = new CreateDwTables( $verbose );
$hierarchy        = new ManagerHierarchy( $verbose );


/*****************************************************************************************************************

  CREATE TABLES

*****************************************************************************************************************/

$dw_table_creator->buildCreateStatements();

/*****************************************************************************************************************

  MANAGER HIERARCHY

*****************************************************************************************************************/

$hierarchy->createHierarchy();
