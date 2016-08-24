<?php

date_default_timezone_set( 'Europe/Madrid' );

//TODO move constants to package.json
define( 'VERSION_NUMBER', 'v1'  );
define( 'DBHOST', 'localhost' );
define( 'DBUSER', 'starter-app-php' );
define( 'DBPASS', 'starter-app-php' );
define( 'DBDB', "starter_app_php" );
define( 'DB_MEMORY_LIMIT', 10000);

///
if(defined('PHPUNIT')){
  $domain="localhost";
}else
  $domain= $_SERVER['SERVER_NAME'];

define( 'LAND', realpath(__DIR__ .'/../..').'/'  );//real base
define( 'BASE', realpath(__DIR__ .'/..').'/'  );//include version
define( 'LAND_URL', $domain);
define( 'BASE_URL', $domain);

define( 'SRC', BASE . 'src/' );

define( 'TPL', LAND . 'tpl/v1/' );

define( 'TMP', LAND . "tmp/" );
define( 'STORE',     LAND     . 'store/' );


if(isset($_GET) && isset($_GET['debug']))
  define( 'DEBUG', true );
else
  define( 'DEBUG', false );//switch to false in production

if( DEBUG ){
  error_reporting(-1);
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

}else
	ini_set( 'display_errors', 'Off' );

require BASE . 'vendor/autoload.php';

if(isset($_GET['show'])){
  $defined= get_defined_constants(true);
  require_once SRC."base/base.php";
  prettyprint_r($defined['user']);

  prettyprint_r($_SERVER);
}


?>
