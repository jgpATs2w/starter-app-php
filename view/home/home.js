angular
  .module('myApp', [])
  .controller('HomeController', HomeController);

function HomeController($scope) {

    $scope.sayHello = function() {
       window.alert("Hello: "+$scope.name);
    }
}
