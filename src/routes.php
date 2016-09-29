<?php
/**
 * @SWG\Info(title="Starter-app-php", version="0.1")
 */
include 'rpc/routes.php';

$app->get('/login', function ($request, $response, $args) {
    //TODO
    echo "Pendiente";
});

$app->get('/', function ($request, $response, $args) {

    return $response->withRedirect('home');
});
/**
 * @SWG\Get(
 *     path="/home",
 *     @SWG\Response(response="200", description="Home is just home")
 * )
 */
$app->get('/home', function ($request, $response, $args) {

    $queryParams= $request->getQueryParams();

    return $this->renderer->render($response, "/home/home.php");
});

$app->get('/info', function ($request, $response, $args) {

    phpinfo();
});

$app->get('/help', function ($request, $response, $args) {
    $swagger = \Swagger\scan(__FILE__);
    //header('Content-Type: application/json');
    prettyprint_r(json_decode($swagger, true));

});
