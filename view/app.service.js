'use strict';

angular
  .module('myApp')
  .service('AppService', AppService);

AppService.$inject= ['AppConfig', '$http'];

function AppService( AppConfig, $http) {

  return {
    getParameterByName: getParameterByName,
    getApiURL: getApiURL,
    getTestCodes: getTestCodes,
    helloServer: helloServer
  }

  ///
  function getParameterByName(name, url) {
      if (!url) url = window.location.href;
      name = name.replace(/[\[\]]/g, "\\$&");
      var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec(url);
      if (!results) return null;
      if (!results[2]) return true;
      return decodeURIComponent(results[2].replace(/\+/g, " "));
  }

  function getApiURL(url){
    var search= window.location.search;
    if(url.indexOf('?')>=0)
      search= search.replace('?', '&');

    return AppConfig.API_URL + url + search;

  }

  function getTestCodes($scope ){

    return $http
            .get( AppConfig.API_URL + 'tests/codes');
  }

  function helloServer($scope){

    return $http
            .get( AppConfig.API_URL + 'hello')
            .then(function(response){
              if(!$scope.ID || $scope.ID=='')
                $scope.ID= response.data.return.IP;
            });
  }

}
