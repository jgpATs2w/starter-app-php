<?php
/*
*   Application independent install file, basically do all the checks required on a fresh install
*/

/////////////////////////   CONFIG    /////////////////////////////////////////////////////
// Settings to make all errors more obvious during testing
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors','On');

date_default_timezone_set( 'Europe/Madrid' );
//error_reporting(-1);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');

global $config_file,$directives_to_check;

$config_file=realpath('../')."/src/conf/config.php";
$directives_to_check = array( "post_max_size", "upload_max_filesize", "memory_limit", "allow_url_fopen", "display_errors", "short_open_tag");

/////////////////////////   SCRIPT    //////////////////////////////////////////////////////

flush_start();

head("configuracion");//TODO: resolve the return in settings
showConfiguration();
showConfigurationApache();

head("permisos");
showUser();
checkPermissions();

head("mysql");
checkMySQL();

head("servidor");
showServerStatus();

?>
<h2>config.js.php</h2>
<p>Leido de <a href="../src/conf/config.js.php">../src/conf/config.js.php</a></p>
<iframe src="../src/conf/config.js.php" width="100%" height="200px"></iframe>

<h2>template</h2>
<p>Leido de <a href="../tpl/evaluacion/_xxDataGlobal/config.php?show">../tpl/evaluacion/_xxDataGlobal/config.php?show</a></p>
<iframe src="../tpl/evaluacion/_xxDataGlobal/config.php?show" width="100%" height="200px"></iframe>

<h2>Package</h2>
<p>Leído de <a href="../tpl/evaluacion/package.json">../tpl/evaluacion/package.json</a></p>
<iframe src="../tpl/evaluacion/package.json" width="100%" height="200px"></iframe>
<?php

///////////////  script functions ///////////////////////////////////////////////////////////////////
function showUser(){
  $f = "/tmp/test.txt";
  touch( $f );

  $user = posix_getpwuid( fileowner( $f) );
  unlink( $f );

  line( "usuario: {$user['name']}" );
}

function showConfiguration(){global $config_file,$directives_to_check;
  if(is_file($config_file)){
    $settings=require $config_file;

    head($config_file,2);

    head("rutas", 3);
    line("LAND: ".LAND);
    line("BASE: ".BASE);
    line("ROOT: ".ROOT);
    line("STORE: ".STORE);
    line("TPL: ".TPL);

    head("data base", 3);
    line("DBHOST: ".DBHOST);
    line("DBUSER: ".DBUSER);
    line("DBPASS: ".DBPASS);
    line("DBDB: ".DBDB);

    head("logs", 3);
    line("LOG: ",false);line(LOG?'true':'false');
    line("LOGDIR: ".LOGDIR);
    line("LOGDB: ".LOGDB);

    head("debug", 3);
    line("DEBUG: ",false);line(DEBUG?'true':'false');
    line("DEBUGECHO: ",false);line(DEBUGECHO?ko('true',false):'false');

  }else{
    die(ko("No existe $config_file"));
  }
  head("php",2);

  ini_set( 'display_errors', 'On' );
  foreach($directives_to_check as $ini)
    line("$ini: " . ini_get($ini));

  line("");
  line("php version: ".phpversion());
}

function showConfigurationApache(){
  head("apache",2);
  print_r(apache_get_modules());
}

function checkPermissions(){
  line("");
  line("revisando permisos de escritura...");
  line("");
  $writable_dirs = array(TMP, STORE, LOGDIR, LOGDB);
  foreach ( $writable_dirs as $dir )
    if(!is_writable( $dir ))
      line(ko($dir));

}

function checkMySQL(){
  line("conexion a MySQL: ",false);
  mysqli_connect(DBHOST,DBUSER,DBPASS,DBDB) or die(ko("no se puede conectar"));
  line(ok(""));
}

function showServerStatus(){
  $df = disk_free_space( LAND );
  $dt = disk_total_space(LAND);
  $du = $dt - $df;
  $dp = sprintf('%.2f',($du / $dt) * 100);

  line("espacio usado en " . LAND . " $dp%");

  $f = LAND;
  $io = popen ( '/usr/bin/du -sh ' . $f, 'r' );
  $size = fgets ( $io, 4096);
  $size = substr ( $size, 0, strpos ( $size, "\t" ) );
  pclose ( $io );

  line('Directory: ' . $f . ' => Size: ' . sprintf( "%0.2f GB", $size / 1000 ));
}

///////////////  helper functions ///////////////////////////////////////////////////////////////////

function flush_start(){

	if (ob_get_level() == 0)
		ob_start();
}

function echo_flush( $msg ){
	echo $msg;
	ob_flush();
	flush();
}
function head( $msg, $level=1){
  echo "<h$level>$msg</h$level>";
}
function line( $msg, $return = true ){
  echo "<span>$msg</span>";
  if($return) echo_flush("<br>");
}
function ok( $msg = "" ){
    echo "<span style='color: green'>OK $msg</span><br>";
};

function ko( $msg = "" ){
    return "<span style='color: red'>KO $msg </span><br>";
};

function oko( $cond ){
  if( $cond() )
  	\assert\ok();
  else
  	\assert\ko();
}
?>
