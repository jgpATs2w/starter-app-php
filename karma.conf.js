//jshint strict: false
module.exports = function(config) {
  config.set({

<<<<<<< HEAD
    basePath: './view',
=======
    basePath: './app',
>>>>>>> 8d4e4642f8b12ffb03e106b7fe8a1d55632159e0

    files: [
      'bower_components/angular/angular.js',
      'bower_components/angular-route/angular-route.js',
      'bower_components/angular-mocks/angular-mocks.js',
<<<<<<< HEAD
      '**/*.js',
      'app.js'
=======
      'components/**/*.js',
      'view*/**/*.js'
>>>>>>> 8d4e4642f8b12ffb03e106b7fe8a1d55632159e0
    ],

    autoWatch: true,

    frameworks: ['jasmine'],

    browsers: ['Chrome'],

    plugins: [
      'karma-chrome-launcher',
      'karma-firefox-launcher',
      'karma-jasmine',
      'karma-junit-reporter'
    ],

    junitReporter: {
      outputFile: 'test_out/unit.xml',
      suite: 'unit'
    }

  });
};
