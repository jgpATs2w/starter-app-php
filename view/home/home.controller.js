'use strict';

angular
  .module('myApp')
  .controller('HomeController', HomeController);

HomeController.$inject= ['$scope', '$timeout', 'HomeService'];

function HomeController($scope, $timeout, HomeService) {
  $scope.name="";

  init();

  ///

  $scope.sayHello= function(){
    HomeService
      .sayHello($scope);
  }

  ///
  function init(){
    message('Escribe tu nombre', 'success');
  }

  function onError( response ){
    message(response.data.message, "danger");
  }

  function message(text, status){
     if(!status) status= "info";
      $scope.message= {
        status: status,
        text: text
      }
  }

}
