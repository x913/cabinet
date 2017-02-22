/**
 * Created by k3kc on 14.02.2017.
 */



var cabinetAppServices = angular.module('cabinetAppServices', ['LocalStorageModule', 'restangular']);

cabinetAppServices.config(['$httpProvider', 'RestangularProvider', function($httpProvider, RestangularProvider) {
    RestangularProvider.setDefaultHttpFields({cache: true});
    $httpProvider.interceptors.push('AuthInterceptor');
}]);



cabinetAppServices.factory('AuthInterceptor', ['$q', '$injector', 'localStorageService', '$location', function($q, $injector, localStorageService, $location) {
    var interceptor = {

        responseError: function(response) {
            if(response.status == 401) {
                console.log('GOT 401, NEED TO REFRESH TOKEN, DUE TO EXPIRED 1');
                localStorageService.remove('token');
                $location.path('/');
                return $q(function () { return null; })
              /*  var $http = $injector.get('$http');
                var token = localStorageService.get('token');
                console.log('OLD TOKEN ' + token);

                $http.get({
                  method: 'GET',
                  url: 'http://project1.dev/api/auth/token',
                  header: {
                      'Authorization': 'Bearer ' + token
                  }
                }).then(function(response) {
                    localStorageService.set('token', response.data.token);
                }, function(response) {
                    console.log('something wrong while refreshing token');
                });*/
            } else {
                return $q.reject(response);
            }
        },
        requestError: function(response) {
            return $q.reject(response);
        },
        response: function(response) {
            var token;

            if(response.data.token) {
                token = response.data.token;
            }

    /*        if(response.headers('Authorization')) {
                token = response.headers('Authorization');
            }
*/
            if(token) {
                localStorageService.set('token', token);
                var Restangular = $injector.get('Restangular');
                Restangular.setDefaultHeaders({'Authorization': 'Bearer ' + token});
            }

            return response;
        }
    };
    return interceptor;
}]);

cabinetAppServices.factory('userService', ['$http', 'localStorageService', 'Restangular', '$cacheFactory', function($http, localStorageService, Restangular, $cacheFactory) {

    var token = localStorageService.get('token');
    if(token) {
        Restangular.setDefaultHeaders({'Authorization': 'Bearer ' + token});
    }

    return {
        isSignedIn: function() {
            if(localStorageService.get('token')) {
                return true
            } else {
                return false;
            }
        },
        signin: function(email, password, onSuccess, onError) {
            $http.post('/api/auth/login', {
                email: email,
                password: password
            }).then(function(response) {
                //localStorageService.set('token', response.data.token);
                //Restangular.setDefaultHeaders({'Authorization': 'Bearer ' + response.data.token});

                var lastLogin = localStorageService.get('login');
                if(lastLogin != email) {
                    // reset cache
                    var httpCache = $cacheFactory.get('$http');
                    httpCache.removeAll();
                }
                localStorageService.set('login', email);
                
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        signup: function(name, email, password, onSuccess, onError) {
            $http.post('/api/auth/signup', {
                name: name,
                email: email,
                password: password
            }).then(function(response) {
                localStorageService.set('token', response.data.token);
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        signout: function() {
            localStorageService.remove('token');
        },
        getToken: function() {
            return localStorageService.get('token');
        },
        refreshToken: function() {
            if(localStorageService.get('token')) {
                $http.get('/api/auth/token', {}).then(function(response) {
                    localStorageService.set('token', response.data.token);
                    onSuccess(response);
                }, function(Response) {
                    onError(response);
                });
            } else {
                // no token in localstorage
                console.log('no token in local storage');
            }
        }
    }
}]);

cabinetAppServices.factory('clientService', ['Restangular', '$cacheFactory', function(Restangular, $cacheFactory) {

    //Restangular.setDefaultHeaders({'Authorization': 'Bearer ' + userService.getToken()});

    return {
        getClients: function(onSuccess, onError) {
            Restangular.one('api/base_clients').get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        getServices: function(onSuccess, onError) {
            Restangular.all('api/services').getList().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        getContracts: function(onSuccess, onError) {

            Restangular.one('api/contracts/').get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        getFixedFees: function(onSuccess, onError) {
            Restangular.one('api/fixed_fees').get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        getBills: function(onSuccess, onError) {
            Restangular.one('api/bills').get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        getBillById: function(id, onSuccess, onError) {
            Restangular.one('api/bills/' + id).get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        getDetails: function(date, onSuccess, onError) {
            Restangular.one('api/details/' + date).get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        getPayments: function(onSuccess, onError) {
            Restangular.all('api/payments').getList().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        updateService: function(service, onSuccess, onError) {
            Restangular.one('api/services/' + service.id).customPUT(service).then(function(response) {
                var httpCache = $cacheFactory.get('$http');
                httpCache.remove('/api/services');
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        }

    }
}]);

