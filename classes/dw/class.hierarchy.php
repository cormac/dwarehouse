<?php
/*****************************************************************************************************************

Created By    : Cormac McGuire - cromac
Created Date  : create manager hierarchy
Description   : 
                 
                
Updated By    :
Updated Date  :
Description   :
*****************************************************************************************************************/

class ManagerHierarchy extends Transformer{

  private $managers;
  private $tree;
  private $h_queries;
  public $bridge_queries = array(
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (1, 1, 0, 0, 1);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (1, 2, 1, 0, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (1, 3, 1, 0, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (1, 4, 2, 0, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (1, 5, 2, 1, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (1, 6, 2, 1, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (1, 7, 2, 1, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (1, 8, 3, 1, 0);",
    
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (2, 2, 0, 0, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (2, 4, 1, 0, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (2, 5, 1, 1, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (2, 6, 1, 1, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (2, 8, 2, 1, 0);",
    
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (3, 3, 0, 0, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (3, 7, 1, 1, 0);",
    
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (4, 4, 0, 0, 0);",
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (4, 8, 1, 1, 0);",
    
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (5, 5, 0, 1, 0);",
    
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (6, 6, 0, 1, 0);",
    
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (7, 7, 0, 1, 0);",
    
    "INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) VALUES (8, 8, 0, 1, 0);",
    
  );
  /**
   *
   */
  public function createHierarchy(){
    /*$this->get_managers();
    krumo( $this->managers );
    $this->createBridgeQueries();*/
    $this->run_bridge_queries();
  }
  
  
  /**
   *
   */
  private function createBridgeQueries(){
    

    
    
  }
  
  
  
  
  /**
   *
   */
  private function get_tree( $mid ){
    $query = sprintf( 
    'SELECT t1.ManagerID AS \'0\', t2.ManagerID as \'1\', t3.ManagerID as \'2\', t4.ManagerID as \'3\' 
    FROM merge_hotel_manager AS t1 
    LEFT JOIN merge_hotel_manager AS t2 ON t2.ResponsibleID = t1.ManagerID 
    LEFT JOIN merge_hotel_manager AS t3 ON t3.ResponsibleID = t2.ManagerID 
    LEFT JOIN merge_hotel_manager AS t4 ON t4.ResponsibleID = t3.ManagerID 
    WHERE t1.ManagerName = \'%s\';'
    , $mid );
    $this->mw_import->executeQuery( $query );
    while($row = mysql_fetch_array( $this->mw_import->__get( 'result' ) ) ){
      $rows[] = $row;
    }
    
    $this->parse_tree($rows);
  }
  
  
  private function parse_tree( $paths, $top = 1, $stop = 0 ){
     $records = array();
     $query = array();
     $i=0;
     $my_top = $top;
     $flag = 1;

     foreach( $paths as $key => $path ){
       

       $k=0;
       
       $my_bottom = 0;
       $mgr = $path[$stop];
       $path = $this->removeNullValues( $path );

       foreach($path as $thing){
         //check for bottom
         if($k == ( count( $path ) - 1 ) )$my_bottom = 1;
        // krumo($records);
         if( $thing && $flag && !array_search( array( $mgr, $thing ), $records ) ){     
           $this->h_queries[] = 
           sprintf( 'INSERT INTO dwarehouse.manager_bridge (parent, child, levels, bottom, top) 
           VALUES (\'%s\', \'%s\', %d, %d, %d);', $mgr, $thing, $k, $my_bottom, $my_top );
           $records[] = array( $mgr, $thing );
           $my_top = 0;
           
         }
         $flag = 1;
         $k++;
       }
       unset( $paths[$i][$stop] ); //remove the top node from the path
       $i++;
       $flag = 0;// make sure we don't self reference the top node again
     }
    
    
    //check if there are any more paths to write up
    $i=0;
    foreach($paths as $path){
      if( $path  == null ){
        unset( $paths[$i] );
      }
      $i++;
    }
    krumo($paths);
    
    if(count( $paths ) ){
      $this->parse_tree($paths, 0, ($stop +1 ) );
    }else{
      foreach ($this->h_queries as $query)echo $query . '<br>';
    }
  }
  
  
  /**
   *
   */
  public function removeNullValues( $array ){
    $i = 0;
    foreach($array as $value){
      
       if( $value === NULL ){
        unset( $array[$i] );
        
       }
       $i++;
    }

    return $array;
  }
  
  /**
   *
   */
  private function add_child_to_tree( $path, $tree ){
    
    foreach( $path as $child){
      
    }
  }
  
  /**
   *
   */
  private function run_bridge_queries(){
    foreach ($this->bridge_queries as $query ){
      $this->mw_import->executeQuery( $query );
    }
  }
  
  
  
  
  /**
   *
   */
  private function getHeadManager(){
    foreach ( $this->managers as $manager ){
      if( $manager->ResponsibleID === NULL )return $manager;
    }
  }
  
  /**
   *
   */
  private function get_managers(){
    $query = 'SELECT * FROM merge_hotel_manager';
    $this->mw_import->executeQuery( $query );
    $result = $this->mw_import->__get( 'result' );
    while( $row[] = mysql_fetch_object( $result ) );
    $this->managers = $row;    

  }
  
  
  
}
