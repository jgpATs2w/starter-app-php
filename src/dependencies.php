<?php

require SRC . 'base/base.php';
require SRC . 'db/db.php';
require SRC . 'session/session.php';
require SRC . 'logger/logger.php';
require SRC . 'tools/tools.php';
require SRC . 'app.php';

$container = $app->getContainer();

$container['renderer'] = function ($c) {
  $settings = $c->get('settings')['renderer'];
  return new Slim\Views\PhpRenderer($settings['template_path']);
};

// errors
$container['errorHandler'] = function($c){
  return function($request, $response, $exception) use ($c)
    {
      $response->getBody()->rewind();

      return $c['response']
        ->withStatus(500)
        ->withJson(getResponse(0,$exception->getMessage()));

    };
};
$container['phpErrorHandler'] = function ($container) {
    return function ($request, $response, $error) use ($container) {
        // retrieve logger from $container here and log the error
        $response->getBody()->rewind();
        return $response->withStatus(500)
                        ->withHeader('Content-Type', 'text/html')
                        ->write("Oops, something's gone wrong!");
    };
};

$container['notFoundHandler'] = function($c){
  return function($request, $response) use ($c)
    {

      $json= array(
        "status"=> 404,
        "developer" => "Ruta no encontrada".PHP_EOL
      );

      return $c['response']
        ->withStatus(404)
        ->withJson($json);
    };
};
