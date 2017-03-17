/**
 * Created by k3kc on 14.02.2017.
 */
var application = angular.module('cabinetApp', ['ngRoute', 'cabinetAppControllers', 'cabinetAppServices']);
application.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {


    $routeProvider
        .when('/signin', {
            templateUrl: 'partials/login.html',
            controller: 'LoginController'
        })
        .when('/signup', {
            templateUrl: 'partials/signup.html',
            controller: 'SignupController'
        })
        .when('/services', {
            templateUrl: 'partials/services.html',
            controller: 'ServiceController'
        })
        .when('/bills', {
            templateUrl: 'partials/bills.html',
            controller: 'BillController'
        })
        .when('/payments', {
            templateUrl: 'partials/payments.html',
            controller: 'PaymentController'
        })
        .when('/', {

            templateUrl: 'partials/index.html',
            controller: 'MainController'
        })
        .otherwise({
            redirectTo: '/'
        });

        $locationProvider.html5Mode(true);
}]);
