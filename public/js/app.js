/**
 * Created by k3kc on 14.02.2017.
 */
var application = angular.module('cabinetApp', ['ui.router', 'cabinetAppControllers', 'cabinetAppServices']);
application.config(['$stateProvider', '$urlRouterProvider', '$locationProvider', function($stateProvider, $urlRouterProvider, $locationProvider, userService) {

    $urlRouterProvider.otherwise('/signin');

    $stateProvider
        .state('signin', {
            url: '/signin',
            templateUrl: 'partials/login.html',
            controller: 'LoginController'
        })
        .state('profile', {
            url: '/profile',
            templateUrl: 'partials/profile.html',
            controller: 'ProfileController',
            resolve: { authenticate: authenticate }
        })
        .state('bills', {
            url: '/bills',
            templateUrl: 'partials/bills.html',
            controller: 'BillController',
            resolve: { authenticate: authenticate }
        })
        .state('payments', {
            url: '/payments',
            templateUrl: 'partials/payments.html',
            controller: 'PaymentController',
            resolve: { authenticate: authenticate }
        })
        .state('services', {
            url: '/services',
            templateUrl: 'partials/services.html',
            controller: 'ServiceController',
            resolve: { authenticate: authenticate }
        });

        function authenticate($q, $state, $timeout, userService) {
            if(userService.isSignedIn()) {
                return $q.when();
            } else {
                $timeout(function() {
                    $state.go('signin');
                });
                return $q.reject();
            }
        }


}]);
