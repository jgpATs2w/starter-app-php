<?php

include 'rpc/routes.php';

$app->get('/login', function ($request, $response, $args) {
    //TODO
    echo "Pendiente";
});

$app->get('/', function ($request, $response, $args) {

    return $response->withRedirect('home');
});

$app->get('/home', function ($request, $response, $args) {

    $queryParams= $request->getQueryParams();

    return $this->renderer->render($response, "/home/home.php");
});

$app->get('/info', function ($request, $response, $args) {

    phpinfo();
});

$app->get('/{codigo:[0-9]+}', function ($request, $response, $args) {

    return $this->renderer->render($response, 'proyecto.php', $args);
});
