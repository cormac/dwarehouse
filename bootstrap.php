<?php

include( 'krumo/class.krumo.php' );
$classes = array( 'mysqlwrapper', 'printer' );

foreach ( $classes as $class ){
  $file = 'classes/bootstrap/class.' . $class . '.php';
  include( $file );
}
