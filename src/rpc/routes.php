<?php


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
