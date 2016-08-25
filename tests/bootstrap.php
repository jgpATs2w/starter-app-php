<?php

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));
define('PHPUNIT', true);

///////////////////////

// Settings to make all errors more obvious during testing
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');
define( 'PRINT_DIE' , false );

require PROJECT_ROOT . '/src/config.php';
require PROJECT_ROOT . '/src/base/base.php';
require PROJECT_ROOT . '/src/db/db.php';
require PROJECT_ROOT . '/src/session/session.php';
require PROJECT_ROOT . '/src/logger/logger.php';
require PROJECT_ROOT . '/src/metrics/metrics.php';

class AppTest extends \PHPUnit_Framework_TestCase{
  function test_structure(){
    $this->assertTrue(true);
  }

  function getResponse($json){
    return get_object_vars( json_decode($json) );
  }
  function getReturn ( $json, $ok = 1 ){
      $Response= $this->getResponse($json);

      return $Response['return'];
  }
  function getResultFromJson ( $json ){
      $Response= $this->getResponse($json);

      return $Response['result'];
  }
  function assertResultOK($json, $message=''){
    $Response= $this->getResponse($json);
    return $this->assertSame($Response['result'], 1, $message.': '.$Response['message']);
  }

  function url_exists($url){//TODO check 404!
    $contents= file_get_contents($url);

    if($contents)
      return true;
    return false;
  }

}
