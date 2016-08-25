<?php
require 'src/config.php';
require 'vendor/autoload.php';//TODO move to config.php
require 'src/base/base.php';
require 'src/db/db.php';
require 'src/session/session.php';
require 'src/logger/logger.php';
require 'src/tools/tools.php';

$settings= array(
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ .'/view/',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'gpo',
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
);

$app = new Slim\App($settings);

//<dependencies
$container = $app->getContainer();

$container['renderer'] = function ($c) {
  $settings = $c->get('settings')['renderer'];
  return new Slim\Views\PhpRenderer($settings['template_path']);
};
// errors
$container['errorHandler'] = function($c){
  return function($request, $response, $exception) use ($c)
    {

      $json= array(
        "status"=> 500,
        "developer" => $exception->getMessage()
      );

      return $c['response']
        ->withStatus(500)
        ->withJson($json);

    };
};

$container['notFoundHandler'] = function($c){
  return function($request, $response) use ($c)
    {

      $json= array(
        "status"=> 404,
        "developer" => "Ruta no encontrada: revisa la sintaxis, solo estan admitidas urls del tipo /, recurso, recursos, recursos/id, recursos/id/recurso".PHP_EOL
      );

      $c['logger']->error("not found");

      return $c['response']
        ->withStatus(404)
        ->withJson($json);
    };
};
//dependencies>


$app->get('/login', function ($request, $response, $args) {
    //TODO
    echo "Pendiente";
});

$app->get('/', function ($request, $response, $args) {

    return $response->withRedirect('home');
});

$app->get('/home', function ($request, $response, $args) {

    $queryParams= $request->getQueryParams();

    return $this->renderer->render($response, "home.php", $args);
});

$app->get('/info', function ($request, $response, $args) {

    phpinfo();
});

$app->get('/{codigo:[0-9]+}', function ($request, $response, $args) {

    return $this->renderer->render($response, 'proyecto.php', $args);
});

$app->get('/rpc', function ($request, $response, $args) {

    return $response->withJson( getResponse(1,'OK', $request->getBody()));
});
$app->post('/rpc', function ($request, $response, $args) {

    return $response->withJson( getResponse(0,'debes especificar la libreria y el metodo en la ruta. P.e. /rpc/base/mock'));
});
$app->post('/rpc/{library}/{method}', function ($request, $response, $args) {

  if(isset($_GET['mock']))
    return $response->withJson( getResponse(1,'OK') );

  $library= $args['library'];
  $file= SRC . "$library/$library.php";
  $method= "\\$library\\" . $args['method'];
  $body= $request->getParsedBody();
  $args= $body['args'];//TODO check

  define('PRINT_DIE', false);

  if(!is_file($file))
    return $response->withJson( getResponse(0, "no existe archivo $file") );

  require_once ( $file );

  if ( function_exists( $method) ){

      return $response->withJson( $method($args) );

  }else
      return $response->withJson( getResponse(0, "el metodo $method no existe"));

  return $response->withJson( getResponse(1,'OK', $request->getParsedBody()));
});

$app->run();
