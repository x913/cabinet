/**
 * Created by k3kc on 14.02.2017.
 */

var cabinetAppControllers = angular.module('cabinetAppControllers', ['angular.filter']);


cabinetAppControllers.controller('PaymentController', ['$scope', 'clientService', function($scope, clientService) {
    clientService.getPayments(function(response) { $scope.payments = response; }, function(response) { console.log('error'); } );

    $scope.subTotal = function(items) {
        var total = 0;
        for(var n in items) {
            total += (items[n].payment_sum * 1);
        }
        return total;
    };

}]);

cabinetAppControllers.controller('LoginController', ['$scope', '$location', 'userService', function($scope, $location, userService) {

    $scope.signin = function() {
        userService.signin($scope.email, $scope.password,
            function(response) {    // onSuccess
                if(response.status == '200') {
                    $location.path('/');
                } else {
                    $scope.error_notify = true;
                    $scope.error_message = "Ошибка авторизации доступа: неверная пара/логин пароль";
                }
            },
            function (response) {
                $scope.error_notify = true;
                $scope.error_message = "Ошибка авторизации доступа: неверная пара/логин пароль";
            });
    };

    $scope.submitDisabled = function() {
        return !$scope.email || !$scope.password;
    };

    $scope.error_notify = false;
    $scope.email = '';
    $scope.password = '';

    if(userService.isSignedIn()) {
        console.log('you are signed in');
        $location.path('/');
    }

}]);

cabinetAppControllers.controller('SignupController', [ '$scope', '$location', 'userService', function($scope, $location, userService) {
    $scope.signup = function() {
        userService.signup($scope.name, $scope.email, $scope.password,
        function(response) {    // onSuccess
            alert('You are now signed in!');
            $location.path('/');
        },
        function (response) {   // onError
            alert('Something was wrong');
        });
    };
    $scope.name = '';
    $scope.email = '';
    $scope.password = '';

    if(userService.isSignedIn()) {
        console.log('you are signed in');
        $location.path('/');
    }

}]);

cabinetAppControllers.controller('BillController', ['$scope', 'clientService', function($scope, clientService) {

    $scope.subTotal = function(items) {
        var total = 0;
        for(var n in items) {
            total += (items[n].bill_sum * 1);
        }
        return total;
    };

    clientService.getClients(function(response) {
        $scope.base_client = response.base_client;
    }, function(response) {
        console.log(response.statusText);
    });

    clientService.getBills(function(response) {
        $scope.bills = response;
    }, function(response) {
        console.log(response.statusText);
    });

    $scope.sortColumnBy = function(newSortingOrder) {
        if($scope.sortingOrder == newSortingOrder) {
            $scope.reverse = !$scope.reverse;
        }
        $scope.sortingOrder = newSortingOrder;
    };

    var loading = false;

    $scope.downloadDetails = function(date) {
        clientService.getDetails(date, function(response) {
            var blob = new Blob([response], {type: "application/pdf"})
            saveAs(blob, 'details_' + date + ".pdf");
        }, function(response) {
            loading = false;
        });
    };

    $scope.downloadBill = function(id) {
            if(loading) {
                alert('Загрузка счета уже идет, ожидайте...');
                return;
            }
            loading = true;

            $('a[data-id="' + id + '"]').removeClass('glyphicon-download-alt');
            $('a[data-id="' + id + '"]').addClass('glyphicon-refresh');

            clientService.getBillById(id, function(response) {
                var blob = new Blob([response], {type: "text/plain;charset=utf-8"})
                saveAs(blob, 'bill_' + id + ".html");
                $('a[data-id="' + id + '"]').removeClass('glyphicon-refresh');
                $('a[data-id="' + id + '"]').addClass('glyphicon-download-alt');
                loading = false;
            }, function(response) {
                loading = false;
        });
    };

}]);

cabinetAppControllers.controller('ServiceController', ['$scope', 'clientService', function($scope, clientService) {

    clientService.getServices(function(response) {
        $scope.services = response;
    }, function(response) {
        console.log(response.statusText);
    });

    $scope.editor = function(item, element) {
        item['editing_' + element] = true;
        item.oldValue = item[element];
    };

    $scope.cancelEditing = function(item, element) {
        item['editing_' + element] = false;
        item[element] = item.oldValue;
    };

    $scope.doneEditing = function(item, element) {
        console.log('doneEditing');
        item['editing_' + element] = false;
        if(item.oldValue != item[element]) {
            clientService.updateService(item, function(response) {}, function(response) {});
        } else {
            console.log('values don`t need to update');
        }
    };


}]);

cabinetAppControllers.controller('NavController', ['$scope', 'userService', '$location', 'clientService', function($scope, userService, $location, clientService) {
    $scope.signout = function() {
        userService.signout();
        console.log('signout');
        $location.path('/signin');
    }

    clientService.getClients(function(response) {
        $scope.base_client = response.base_client;
    });

}]);

cabinetAppControllers.controller('MainController', ['$scope', 'userService', '$location', 'clientService', function($scope, userService, $location, clientService) {
    if(!userService.isSignedIn()) {
        console.log('You are not signed in');
        $location.path('signin');
        return;
    }

    $scope.formatContract = function(contract)
    {
        var item =  contract.contract_title.replace('&#8470;', 'N ');
        return item.charAt(0).toUpperCase() + item.slice(1);
    };

    clientService.getClients(function(response) {
        $scope.base_client = response.base_client;

        clientService.getContracts(function(response) {
            $scope.contracts = response;

            clientService.getFixedFees(function(response) {
                $scope.fixed_fees = response;
            }, function(response) {
                console.log(response.statusText);
            });


        }, function(response) {
            console.log(response.statusText);
        });

    }, function(response) {
        console.log(response.statusText);
    });

}]);

