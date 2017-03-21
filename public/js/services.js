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
                localStorageService.remove('token');
                $location.path('/signin');
                return $q(function () {
                    return null;
                })

            } else if(response.status == 400) { // && (response.data.error == 'token_invalid' || response.data.error == 'token_not_provided')) {
                localStorageService.remove('token');
                $location.path('/signin');
                return $q(function () {
                    return null;
                })
            } else if (response.status == 429) {
                alert('Для вашего IP адреса сработало ограничение на количество запросов к API сервиса, попробуйте повторить ваш запрос через несколько минут');
                return $q.reject(response);
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
        signin: function(credentials, onSuccess, onError) {
            $http.post('/api/auth/login', {
                credentials: credentials
            }).then(function(response) {
                var lastLogin = localStorageService.get('login');
                if(lastLogin != credentials.email) {
                    // reset cache
                    var httpCache = $cacheFactory.get('$http');
                    httpCache.removeAll();
                }
                localStorageService.set('login', credentials.email);
                
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
            }
        }
    }
}]);

cabinetAppServices.factory('clientService', ['Restangular', '$cacheFactory', 'userService', function(Restangular, $cacheFactory, userService) {

    return {
        getClient: function(onSuccess, onError) {

            if(!userService.isSignedIn()) {
                return;
            }

            Restangular.one('api/base_clients').get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        updateClient: function(client, onSuccess, onError) {
            Restangular.one('api/base_clients/' + client.client_id).customPUT(client).then(function(response) {
                var httpCache = $cacheFactory.get('$http');
                httpCache.remove('/api/base_clients');
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        addEmail: function(email, onSuccess, onError) {
            Restangular.one('api/base_clients/email/').customPOST(email).then(function(response) {
                var httpCache = $cacheFactory.get('$http');
                httpCache.remove('/api/base_clients');
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        removeEmail : function(email, onSuccess, onError) {
            Restangular.one('api/base_clients/email/' + email.id).remove().then(function(response) {

                var httpCache = $cacheFactory.get('$http');
                httpCache.remove('/api/base_clients');
                onSuccess(response);

            }, function(response) {
                onError(response);
            });
        }
    }
}]);

cabinetAppServices.factory('billService', ['Restangular', '$cacheFactory', function(Restangular, $cacheFactory) {
    return {
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
            Restangular.one('api/details/' + date).withHttpConfig({responseType: 'arraybuffer'}).get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        }
    }
}]);

cabinetAppServices.factory('contractService', ['Restangular', '$cacheFactory', function(Restangular, $cacheFactory) {
    return {
        getContracts: function(onSuccess, onError) {

            Restangular.one('api/contracts/').get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        }
    }
}]);

cabinetAppServices.factory('paymentService', ['Restangular', '$cacheFactory', function(Restangular, $cacheFactory) {
    return {
        getPayments: function(onSuccess, onError) {
            Restangular.all('api/payments').getList().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        }
    }
}]);

cabinetAppServices.factory('feeService', ['Restangular', '$cacheFactory', function(Restangular, $cacheFactory) {
    return {
        getFixedFees: function(onSuccess, onError) {
            Restangular.one('api/fixed_fees').get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        }
    }
}]);

cabinetAppServices.factory('phoneService', ['Restangular', '$cacheFactory', function(Restangular, $cacheFactory) {
    return {
        get: function(onSuccess, onError) {
            Restangular.one('api/services').get().then(function(response) {
                onSuccess(response);
            }, function(response) {
                onError(response);
            });
        },
        update: function(service, onSuccess, onError) {
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