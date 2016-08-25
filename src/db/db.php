<?php
namespace db;

DB::connect();

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

class DB{
    public static $con = null;
    public static $result = null;
    private static $lastQuery= null;

    static function connect(){
      try{
        self::$con = new \PDO("mysql:host=localhost;dbname=".DBDB, DBUSER, DBPASS);

        //select the type of error produced
        self::$con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

      }catch(\PDOException $e){
        die("No se ha podido conectar a la base de datos '".$e->getMessage()."'");
      }
    }
    /*
    * @param query complete query.
    * @return void
    */
    static function query($query){
      try{
        self::$result= self::$con->query($query);
        self::$lastQuery= $query;

      }catch(\PDOException $e){
        die("DB.query# no se ha podido ejecutar la query '$query, produciendo el error ' '".$e->getMessage()."'");
      }
    }

    /*
    * Warning: only works once per query. To reuse the result, iterate over the result->fetch() directly
    * @param className Specifies the name of class to create the objects, 'stdClass' by default.
    * @return array of objects
    */
    static function getSingle(){
      try{
        if(is_object(self::$result)){

          return self::$result->fetchColumn(0);
        }else
          die("DB.getArray# el resultado del query '".self::$lastQuery."' no es un objeto, tiene el valor '".self::$result."'");

      }catch(\PDOException $e){
        die("DB.query# no se ha podido ejecutar la query '$query, produciendo el error ' '".$e->getMessage()."'");
      }
    }
    /*
    * Warning: only works once per query. To reuse the result, iterate over the result->fetch() directly
    * @param className Specifies the name of class to create the objects, 'stdClass' by default.
    * @return array of objects
    */
    static function getArray(){
      try{
        if(is_object(self::$result)){

          return self::$result->fetchAll();
        }else
          die("DB.getArray# el resultado del query '".self::$lastQuery."' no es un objeto, tiene el valor '".self::$result."'");

      }catch(\PDOException $e){
        die("DB.query# no se ha podido ejecutar la query '$query, produciendo el error ' '".$e->getMessage()."'");
      }
    }

    /*
    * Warning: only works once per query. To reuse the result, iterate over the result->fetch() directly
    * @param className Specifies the name of class to create the objects, 'stdClass' by default.
    * @return array of objects
    */
    static function getObjects( $className = "stdClass" ){
      try{
        if(is_object(self::$result)){

          return self::$result->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);
        }else
          die("DB.getArray# el resultado del query '".self::$lastQuery."' no es un objeto, tiene el valor '".self::$result."'");

      }catch(\PDOException $e){
        die("DB.query# no se ha podido ejecutar la query '$query, produciendo el error ' '".$e->getMessage()."'");
      }
    }

    /*
    * FIXME does not allow to use variables from outside, by now is only a reference of good code
    * Calls passed function on every table's row
    * @param callable function that receives $row as argument
    * @return void
    */
    static function walk($callable){
      try{
        if(is_callable($callable)){
          if(is_object(self::$result)){

            while( $row= self::$result->fetch()){
              $callable($row);
            }

          }else
            die("DB.getArray# el resultado del query '".self::$lastQuery."' no es un objeto, tiene el valor '".self::$result."'");

        }else
          die("DB.walk# se debe pasar una función como argumento");

      }catch(\PDOException $e){
        die("DB.query# no se ha podido ejecutar la query '$query, produciendo el error ' '".$e->getMessage()."'");
      }
    }
    static function transact($queries){
      try{
        self::$con->beginTransaction();
        foreach($queries as $query){
          self::$con->exec($query);
        }
        self::$con->commit();
      }catch(\PDOException $e){
        self::$con->rollback();

        die("DB.transact# error en transaccion, rolledback '".$e->getMessage()."'");
      }
    }

    static function last_error(){
      return mysqli_error(self::$con);
    }


}
?>
