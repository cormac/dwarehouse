<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 01/05/2011
Description   : Transform the data from the etl tables to the star schema
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

$dw_classes = array( 'createdwtables', 'hierarchy', 'dimension_upload' );

foreach ( $dw_classes as $class ){
  $file = 'classes/dw/class.' . $class . '.php';
  include( $file );
}



$verbose          = true;
$dw_table_creator = new CreateDwTables( $verbose );
$hierarchy        = new ManagerHierarchy( $verbose );
//$dim_upload       = new DimensionUpload( $verbose );

/*****************************************************************************************************************

  CREATE TABLES

*****************************************************************************************************************/

$dw_table_creator->buildCreateStatements();

/*****************************************************************************************************************

  UPLOAD DATA

*****************************************************************************************************************/

//$upload->write_to_warehouse();

/*****************************************************************************************************************

  MANAGER HIERARCHY

*****************************************************************************************************************/

$hierarchy->createHierarchy();
