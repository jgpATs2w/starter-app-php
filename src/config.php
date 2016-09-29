<?php

/*
* Static parameters: review and adapt
*/
define( 'VERSION_NUMBER', 'v1'  );
define( 'VERSION', '1.0'  );

//DB parameters
define( 'DBHOST', 'localhost' );
define( 'DBUSER', 'starter-app-php' );
define( 'DBPASS', 'starter-app-php' );
define( 'DBDB', "starter-app-php" );
define( 'DB_MEMORY_LIMIT', 10000);

//url web server root
define('HTTP_URL', 'http://domain/starter-app-php/');

//Slim settings
$settings= array(
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ .'/../view/',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'gpo',
            'path' => __DIR__ . '/../../logs/app.log',
        ],
    ],
);

///

date_default_timezone_set( 'Europe/Madrid' );

if(defined('PHPUNIT')){
  $domain=str_replace('domain', 'localhost', HTTP_URL);
}else
  $domain=str_replace('domain', $_SERVER['SERVER_NAME'], HTTP_URL);

define( 'LAND', realpath(__DIR__ .'/../..').'/'  );//useful for directories shared between versions
define( 'BASE', realpath(__DIR__ .'/..').'/'  );//include version
define( 'LAND_URL', $domain);
define( 'BASE_URL', $domain . VERSION_NUMBER);

define( 'SRC', BASE . 'src/' );

define( 'TMP', LAND . "tmp/" );
define( 'STORE', LAND . 'store/' );

/*
* Debugging, set statically with defined or dinamically with ?debug
*/
// Mock state: use to return quick responses to test integration
if(isset($_GET) && isset($_GET['mock']))
  define( 'MOCK', true );
else
  if(!defined('MOCK'))
    define( 'MOCK', false );
  //allow phpunit to define

// Debug state
if(isset($_GET) && isset($_GET['debug']))
  define( 'DEBUG', true );
else
    if(!defined('DEBUG'))
      define( 'DEBUG', false );//switch to false in production
    //allow phpunit to define

error_reporting(-1);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

//Prepare third party tools autoload, configured in composer.json
if(is_file( BASE . 'vendor/autoload.php'))
  require BASE . 'vendor/autoload.php';

//With show parameter in url, this script will show
if(isset($_GET['showConfig'])){
  $defined= get_defined_constants(true);
  require_once SRC."base/base.php";
  prettyprint_r($defined['user']);

  prettyprint_r($_SERVER);
}


?>
