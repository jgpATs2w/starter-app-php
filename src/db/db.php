<?php
namespace db;

DB::connect();

function query($q, $safe=false){
  DB::query($q);
}
function are_results(){
    return is_object(DB::$result);
}
function query_single($q){

	DB::query($q);

  return DB::getSingle();

}
//@deprecated don't use anymore, use DB::getArray or DB::getObjects
function query_array( $q ){

    DB::query($q);
    return DB::getArray();
}

function get_array_full( $onelevel = false, $clean= false, $keyColumn=false, $arrayToMerge= false ){

    $array = array();
    $i = 0;

  	if( \db\DB::$result ){
  		while( $row = DB::$result->fetch() ){

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
function execute($q){

  DB::query($q);

	return true;

}

function last_id(){
	return mysqli_insert_id(  \db\DB::$con );
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
        throw new \Exception("No se ha podido conectar a la base de datos '".$e->getMessage()."'");
      }
    }

    static function lastId(){
      try{
        return self::$con->lastInsertId();

      }catch(\PDOException $e){
        throw new \Exception("DB.lastId# '".$e->getMessage()."'");
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
        throw new \Exception("DB.query# no se ha podido ejecutar la query '$query, produciendo el error ' '".$e->getMessage()."'");
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
          throw new \Exception("DB.getArray# el resultado del query '".self::$lastQuery."' no es un objeto, tiene el valor '".self::$result."'");

      }catch(\PDOException $e){
        throw new \Exception("DB.query# no se ha podido ejecutar la query '".self::$lastQuery."', produciendo el error ' '".$e->getMessage()."'");
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
          throw new \Exception("DB.getArray# el resultado del query '".self::$lastQuery."' no es un objeto, tiene el valor '".self::$result."'");

      }catch(\PDOException $e){
        throw new \Exception("DB.query# no se ha podido ejecutar la query '".self::$lastQuery."', produciendo el error ' '".$e->getMessage()."'");
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
          throw new \Exception("DB.getArray# el resultado del query '".self::$lastQuery."' no es un objeto, tiene el valor '".self::$result."'");

      }catch(\PDOException $e){
        throw new \Exception("DB.query# no se ha podido ejecutar la query '$query, produciendo el error ' '".$e->getMessage()."'");
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
            throw new \Exception("DB.getArray# el resultado del query '".self::$lastQuery."' no es un objeto, tiene el valor '".self::$result."'");

        }else
          throw new \Exception("DB.walk# se debe pasar una función como argumento");

      }catch(\PDOException $e){
        throw new \Exception("DB.query# no se ha podido ejecutar la query '$query, produciendo el error ' '".$e->getMessage()."'");
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

        throw new \Exception("DB.transact# error en transaccion, rolledback '".$e->getMessage()."'");
      }
    }

    static function last_error(){
      return mysqli_error(self::$con);
    }


}
?>
