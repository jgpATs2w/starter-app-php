<?php
namespace db;

\db\connect();//TODO best place??
function connect( ){

    \db\DB::$con = mysqli_connect(DBHOST, DBUSER, DBPASS, DBDB ) or die("No se pudo conectar a la base de datos '".DBDB."'");
	if (!\db\DB::$con->set_charset("utf8")) {
	    printf("Error cargando el conjunto de caracteres utf8: %s\n", \db\DB::$con->error);
	}
}
function query($q, $safe=false){

  if(strlen($q) > DB_MEMORY_LIMIT){

    \logger\error("db\query",  "query exceeded DB_MEMORY_LIMIT");
    return false;
  }

  \db\DB::$result = mysqli_query( \db\DB::$con, $q);

	if(_check( \db\DB::$result, $q )){
    return \db\DB::$result;
  }

  if(!$safe)
    \logger\error("db.query", $q . "produced a fatal error" .\db\DB::last_error());

  return false;
}
function are_results(){
    if( \db\get_array_full() )
        return true;

    return false;
}
function query_single($s){

	$result = mysqli_query( \db\DB::$con, $s );

	_check($result, $s);
	if( $result ){

		$return = mysqli_fetch_array($result);
	  return $return[0];
	}else
		return false;

}
function query_array( $s ){ \logger\debug( 'db.query_array', $s );

    $result = mysqli_query( \db\DB::$con, $s );

	  _check($result);

    return mysqli_fetch_assoc($result);//TODO remove indexed items

}

function get_array_full( $onelevel = false, $clean= false, $keyColumn=false, $arrayToMerge= false ){

    $array = array();
    $i = 0;
  	$result = \db\DB::$result;

    _check($result);

  	if( $result ){
  		while( $row = \db\DB::$result->fetch_array() ){//TODO remove indexed items

        if($keyColumn){
          $key= $row[$keyColumn];
        }else
  	      $key= $i;

        if( $onelevel){
  				$content = $row[0];
        }elseif($arrayToMerge && array_key_exists($key, $arrayToMerge)){
          $content= array_merge($arrayToMerge[$key], $row);
        }else
          $content= $row;

        $array[$key]= $content;

  	    $i++;
  	  }

  		\db\DB::$result->free();
  	}
    if($clean){
      foreach($array as $k=>$v){
        foreach($v as $kk=>$vv){
          if(is_numeric($kk))
              unset($array[$k][$kk]);

        }
      }
    }

    return $array;

}
function execute($s){ //\logger\debug( 'db.execute', $s );

    $result =  mysqli_query( \db\DB::$con, $s );

	_check( $result, $s );

	return $result;

}

function last_id(){
	return mysqli_insert_id(  \db\DB::$con );
}

function _check( $result, $q = "" ){
	if( $result === false ){
		///throw new \Exception( "db._check# error_mysql: '$q'. Produced MySQL error: '".\db\DB::last_error()."'" );//FIXME Fatal error: Call to undefined function Exception()
    //\logger\log2file(" error_mysql: '$q'");
    printJson(0,1,"db._check# error_mysql: '$q'. Produced MySQL error: '".\db\DB::last_error()."'" );
    return false;
  }
  return true;
}

/* DB container, just support 1 connection */
class DB{
    public static $con = null;
    public static $result = null;

    static function last_error(){
      return mysqli_error(self::$con);
    }
}
?>
