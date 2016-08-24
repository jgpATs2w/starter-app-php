<?php

function echo_debug(){
  echo "LAND: ".LAND;
	echo "BASE: ".BASE;
	echo "STORE: ".STORE;
	echo "DBHOST: ".DBHOST;
	echo "DBUSER: ".DBUSER;
	echo "DBPASS: ".DBPASS;
	echo "DBDB: ".DBDB;
	echo "DEBUG: ",false;echo DEBUG?'true':'false';
}
function prettyprint_r($array){
 foreach($array as $key => $value){
   if(is_array($value)){
     printf("<b>&nbsp;%s</b>[<br>", $key);
     prettyprint_r($value);
     echo "]<br>";
   }else if(is_object($value)){
     var_dump($value);
   }else
     printf("<b>%s</b>: %s<br>", $key, $value);
 }
}

function sanitize_filename( $file ){
	$file = str_replace(' ', '_', $file );
 $file = preg_replace("/[^\w\d\-_\.]*/", '', $file);
	$file = preg_replace("([\._]{2,})", '_', $file);
	$file = trim($file, " _\.");

	return $file;
}

function error_handler($errno, $errstr, $errfile, $errline) { global $trace;

    $errstr = "ERROR: " . $errno ."; $errstr on $errfile line $errline" ;

	logError( $errstr );
    switch ($errno) {
        case E_USER_WARNING:
            $trace .= $errstr;
            break;

        case E_USER_NOTICE:
        case E_USER_ERROR:
        default:
            traceAdd( $errstr );
    }
}
function fatal_handler( $exception ) {global $trace;//TODO make it work
  $errfile = "unknown file";
  $errstr  = "shutdown";
  $errno   = E_CORE_ERROR;
  $errline = 0;

  $error = error_get_last();

  if( $error !== NULL) {
    $errno   = $error["type"];
    $errfile = $error["file"];
    $errline = $error["line"];
    $errstr  = $error["message"];

    $errstr = $errno ."; $errstr on $errfile line $errline" ;
	logError( $errstr );
     printJson( 0, $errstr , $trace );
  }
}

function shutdown_handler(){

	logError( "shutdown" );

	printJson( 0, "shutdown" );
	//die();
}
//http://php.net/manual/es/function.scandir.php
function dirToArray($dir ) {

   $result = array();

   $cdir = scandir($dir);
   foreach ($cdir as $key => $value)
   {
      if (!in_array($value,array(".","..")))
      {
         if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
         {
            $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
         }
         else
         {
            $result[] = $value;
         }
      }
   }

   return $result;
}

function testPost( $POST ){
    $test= $POST['test'] or printJson( 0, 'falta test en POST', null );

    printJson(1, 'OK', $test);
}
function testGet( ){
    printJson(1, 'OK', $test);
}
function testEmptyResponse(){
	die();
}

function sh( $cmd, $log = "shell" ){

	\logger\debug( $log,  $cmd );

  if(is_writable(LOGDIR . "$log.log"))
	 $ret = exec( $cmd . " >> " . LOGDIR . "$log.log 2>&1" );
  else
	 $ret = exec( $cmd );

	return $ret;
}

function logError( $message, $logName = "errors" ){
	\logger\error( $logName,  $message );
}

function logto( $name, $message, $error = false, $date = true ){

   \logger\logto( $name, $message, $error );
}

function logRemote( $message ){
	logto( "remote", $message );
}

function printSession(){
    printJson(1, 'OK', $_SESSION );
}

function toolsUpdateConfig(){ global $trace;
 $store = $_GET['store'] or printJson( 0, 'falta store en url', false );
 if( $store == '*' ){

    traceAdd( 'updating all' );
 }else{
     if( ! is_dir( "store/$store" ) )
        printJson(0,"$store not exists", null);
 }
 foreach ( glob( "store/$store" ) as $i => $v ){
     $dir = "$v/config";
     traceAdd( "updating $v..." );
     if ( is_dir( $dir ) ) {
         rmdirr( $dir );

         cpr( "tpl/solucion/config", $dir );

        traceAdd( 'ok' );
     }else
        traceAdd( "<span style=\"color:red\">$dir not exists</span>" );

 }

 printJson(1,'OK', null);
}

/** AUX **/

function flush_start(){

	if (ob_get_level() == 0)
		ob_start();
}

function echo_flush( $msg ){
	echo $msg .PHP_EOL;
	flush();
	ob_flush();
}

function flush_end(){

	ob_end_flush();
}

function cpr_2($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                cpr($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}
function mkdirr( $dir ){

    if( !is_dir($dir))
        if( ! mkdir($dir, 0755, true) )
			echo 'fail to create dir ' . $dir;

}
function cpr( $source, $dest){ //timezone error in server
    if( !is_dir($dest))
        mkdir($dest, 0755, true);

    foreach (
      $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($source, 4096),
      RecursiveIteratorIterator::SELF_FIRST) as $item) {
      	  $file = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
	      if ($item->isDir()) {
	      	if( ! is_dir( $file ) )
	        	mkdir( $file );
	      } else {
	        	copy($item, $file );
      }
    }
	return true;
}

function rmdirr( $dir , $restrictBase = true, $base = LAND ){
	//never delete files outside the BASE dir
	if( $restrictBase && strpos( $dir, $base ) === false ){
		traceAdd( "$dir not in " . BASE );
		return false;
	}

  exec("rm -r $dir");
}

function traceAdd ( $msg ){ global $trace;
    $trace .= ";$msg;\n";
}

function cancel_get_file( $process ){
	return TMP . "kill_$process";
}
function cancel_check( $process ){
	$kf = cancel_get_file($process);

	if( is_file( $kf ) ){
		unlink( $kf );
		die();
	}

}
function getResponse($result , $m , $return = null){
  $R = array();
      $R[ 'result' ] = $result;
      $R[ 'message' ] = $m;
      $R[ 'return' ] = $return;

  return $R;
}
function printJson( $result , $m , $return = null ){
    $R = array();
        $R[ 'result' ] = $result;
        $R[ 'message' ] = $m;
        $R[ 'return' ] = $return;

    if( PRINT_DIE )
        die( json_encode( $R ) );
    else
        return ( json_encode( $R ) );
};
?>
