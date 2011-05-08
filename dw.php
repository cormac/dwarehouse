<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : 01/05/2011
Description   : Transform the data from the etl tables to the star schema
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

$dw_classes = array( 'createdwtables', 'hierarchy', 'dimension_upload', 'fact_upload' );

foreach ( $dw_classes as $class ){
  $file = 'classes/dw/class.' . $class . '.php';
  include( $file );
}



$verbose          = true;
$dw_table_creator = new CreateDwTables( $verbose );
$hierarchy        = new ManagerHierarchy( $verbose );
$dim_upload       = new DimensionUpload( $verbose );
$fact_upload      = new FactUpload( $verbose );


/*****************************************************************************************************************

  CREATE TABLES

*****************************************************************************************************************/

$dw_table_creator->buildCreateStatements();

/*****************************************************************************************************************

  UPLOAD DATA

*****************************************************************************************************************/

$dim_upload->write_to_warehouse();
$fact_upload->write_facts();


/*****************************************************************************************************************

  MANAGER HIERARCHY

*****************************************************************************************************************/

$hierarchy->createHierarchy();
