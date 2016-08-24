<?php
namespace metrics;

function increase($name){
  $name= \mysqli_real_escape_string(\db\DB::$con, $name);
  $q= "INSERT INTO metrics (name, value_number) VALUES ('$name', 1)
        ON DUPLICATE KEY UPDATE value_number = value_number + 1";

  \db\query($q);
}

function set($name, $value){
  $q= "INSERT INTO metrics (name, value_text) VALUES ('$name', '$value')
        ON DUPLICATE KEY UPDATE value_text = '$value'";
        
  \db\query($q);
}

function get($name){
  $q= "select * from metrics where name='$name'";

  return \db\query_single($q);
}

?>
