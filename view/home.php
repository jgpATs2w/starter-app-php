<!DOCTYPE html>
<html ng-app="myApp">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title class="app-name">starter-app-php</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="view/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="view/app.css">
  <style>
  .full button span {
    background-color: limegreen;
    border-radius: 32px;
    color: black;
  }
  .partially button span {
    background-color: orange;
    border-radius: 32px;
    color: black;
  }
</style>
</head>

<body>


<div class="container" ng-cloak ng-controller="HomeController">
	<h1>Starter-app-php</h1>
	<p>
		Hola {{name}}
	</p>
  <input ng-model="name" type="text" name="name"><button type="button" ng-click="sayHello()">Greet</button>

<div class="row">
  <label for="">
    Desde:
    <select>
      <option ng-repeat="n in [].constructor(24) track by $index" value="{{$index}}">{{$index}}</option>
    </select>
    :
    <select>
      <option ng-repeat="n in [].constructor(60) track by $index" value="{{$index}}">{{$index}}</option>
    </select>
  </label>
</div>

<div class="row">
  <label for="">
    Hasta:
    <select>
      <option ng-repeat="n in [].constructor(12) track by $index" value="{{$index}}">{{$index}}</option>
    </select>
    :
    <select>
      <option ng-repeat="n in [].constructor(60) track by $index" value="{{$index}}">{{$index}}</option>
    </select>
  </label>
</div>

</div>



<script type="text/javascript" src="view/bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="view/bower_components/angular/angular.min.js"></script>
	<script type="text/javascript" src="view/bower_components/angular-animate/angular-animate.min.js"></script>
  	<script type="text/javascript" src="view/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="view/bower_components/angular-bootstrap/ui-bootstrap.min.js"></script>

	<script type="text/javascript" src="view/config.js.php"></script>

	<script type="text/javascript" src="view/app.js"></script>

  <script type="text/javascript">

  angular
    .module('myApp', [])
    .controller('HomeController', HomeController);

  function HomeController($scope) {
	    $scope.contacts = ["hi@email.com", "hello@email.com"];

	    $scope.sayHello = function() {
    	   window.alert("Hello: "+$scope.name);
	    }
	}

</script>

</body>
