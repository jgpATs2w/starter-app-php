'use strict';

angular
  .module('myApp')
  .service('HomeService', HomeService);

HomeService.$inject= ['$http', '$timeout', '$window', 'AppConfig'];

function HomeService( $http, $timeout, $window, AppConfig) {

  return {
    sayHello: sayHello
  }

  ///
  function sayHello( $scope ){
    window.alert("Hello: "+$scope.name);
  }

}
