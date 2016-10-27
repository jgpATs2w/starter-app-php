<!DOCTYPE html>
<html ng-app="myApp">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title class="app-name">[AppTitle]</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="view/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="view/app.css">

</head>

<body ng-cloak ng-controller="HomeController">

  <div class="container-fluid">
    <div class="row-fluid">
      <h1>Starter-app-php</h1>
      <p>
        Hola {{name}}
      </p>
      <input ng-model="name" type="text" name="name"><button type="button" ng-click="sayHello()">Greet</button>

    </div>

    <div class="row-fluid">
      <div ng-show="message.text" ng-click="message.text=''" class="well bg-{{message.status}}">
        {{message.text}}
      </div>
    </div>

    <!--debug-->
    <div ng-show="DEBUG" class="row-fluid">
      <div class="btn-group" role="group">
        <h2>Depuración</h2>

        <p>
          do something special in debug mode
        </p>
      </div>
    </div>
  </div>

	<script type="text/javascript" src="view/bower_components/angular/angular.min.js"></script>

	<script type="text/javascript" src="view/app.js"></script>
	<script type="text/javascript" src="view/app.service.js"></script>

	<script type="text/javascript" src="view/home/home.service.js"></script>
  <script type="text/javascript" src="view/home/home.controller.js"></script>

</body>
