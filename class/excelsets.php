<?php


class ExcelSets {

  private static $setname = -1 ;
  private static $newset  = 0 ;

  public static function getSets() {
  
    $query = "SELECT set_id, set_name FROM EE2_sets" ;
    $query = mysql_query( $query ) or die( error_log( "ExcelSets::getSets(): " . mysql_error()  )  ) ;

    $setnames = array() ;
	
    while( $temp = mysql_fetch_array( $query, MYSQL_ASSOC )  ) {
	
	  $setnames[ $temp['set_id'] ] = $temp['set_name'] ;
	
	}

	return $setnames ;
	
  }
  

  public static function initSetByName( $setname ) {

    $setname = mysql_real_escape_string( $setname ) ;
    $query = "SELECT set_id FROM EE2_sets WHERE set_name='".$setname."' ;" ;
    $query = mysql_query( $query ) or die( "ExcelSets::newSet() - query 1: " . mysql_error() ) ;
    
    while( $temp = mysql_fetch_array( $query, MYSQL_ASSOC ) ) {
	  $set_id = $temp[ 'set_id' ] ;
	}
  
    if ( !isset($set_id) ) {
      $query = "INSERT INTO EE2_sets (set_id, set_name) values( DEFAULT, 'Current DB upload (".date('Y-m-d').")' ) ;" ;
	  mysql_query( $query ) or die( "ExcelSets::newSet() - query 2: " . mysql_error() ) ;
	  $set_id = mysql_insert_id() ;
	  ExcelSets::$newset = 1 ;
    }
	
	ExcelSets::$setname = $set_id ;
  }

  public static function removeAllOtherSets() {
	 // echo "SET NAME: " . ExcelSets::$setname . "<br>" ;

	if ( ExcelSets::$setname != -1 ) {
	  $query = "DELETE FROM EE2_values WHERE set_id != '".ExcelSets::$setname."'" ;
	  mysql_query( $query ) or die( mysql_error() ) ;		
		
      $query = "DELETE FROM EE2_sets WHERE set_id != '".ExcelSets::$setname."' ;" ;
	  mysql_query( $query ) or die( mysql_error() ) ;
	  

	}
	 // die() ;
  }
  
  public static function setSetId( $set_id ) {
    ExcelSets::$setname = $set_id ;
  }
  
  public static function newSetStatus() {
    return ExcelSets::$newset ;
  }
  
  public static function getSetId() {
    //error_log( "getSetId check: " . ExcelSets::$setname ) ;
    return ExcelSets::$setname ;
  }


}


?>