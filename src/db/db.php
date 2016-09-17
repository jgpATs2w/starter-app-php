<?php
namespace db;

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
      if(is_null(self::$con))
        DB::connect();
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
      if(is_null(self::$con))
        DB::connect();
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
      if(is_null(self::$con))
        DB::connect();
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
      if(is_null(self::$con))
        DB::connect();
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
      if(is_null(self::$con))
        DB::connect();
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
      if(is_null(self::$con))
        DB::connect();

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
      if(is_null(self::$con))
        DB::connect();

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
