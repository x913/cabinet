/**
 * Created by k3kc on 14.02.2017.
 */
//var filter = angular.module('');

var cabinetAppControllers = angular.module('cabinetAppControllers', ['angular.filter']);

cabinetAppControllers.filter('sumTotal', function() {
   return function (list, sumField) {
       var total = 0;
       angular.forEach(list, function(item) {
          total += (item[sumField] * 1);
       });
       return total;
   }
});

cabinetAppControllers.filter('groupByFieldEx', function() {
    return function(list, groupBy) {
        var filtered = [];
        angular.forEach(list, function(item) {
            var seen = false;
            for(var i = 0; i < filtered.length; i++) {
                if(filtered[i] == item[groupBy]) {
                    seen = true;
                    break;
                }
            }
            if(!seen)
                filtered.push(item[groupBy]);
        });
        return filtered;
    }
});

cabinetAppControllers.filter('groupByField', function() {
    return function(list, groupBy, sumBy) {

        function sumTotal(currentItem) {
            var totalSum = 0;
            angular.forEach(list, function(item) {
                if(item[groupBy] == currentItem[groupBy])
                    totalSum += item[sumBy] * 1;
            });
            return totalSum;
        }

        var filtered = [];
        var prevItem = null;
        var groupChanged = false;
        var changeField = groupBy + '_changed';
        angular.forEach(list, function(item) {
            groupChanged = false;

            if(prevItem != null) {
                if(prevItem[groupBy] !== item[groupBy]) {
                    groupChanged = true;
                    item[changeField] = true;
                    item.total_sum = sumTotal(item);
                }
            } else {
                groupChanged = true;
                item.total_sum = sumTotal(item);
            }

            if(groupChanged) {
                item[changeField] = true;
            } else {
                item[changeField] = false;
            }
            filtered.push(item);
            prevItem = item;
        });
        return filtered;
    }
});

cabinetAppControllers.controller('PaymentController', ['$scope', 'paymentService', function($scope, paymentService) {

    paymentService.getPayments(function(response) {
        $scope.payments = response;
    }, function(response) {

    });

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

cabinetAppControllers.controller('BillController', ['$scope', 'billService', function($scope, billService) {

    $scope.isEven = function(index) {
        return index % 3 == 0;
    };

    $scope.downloadBill = function(id) {
        billService.getBillById(id, function(response) {
            var blob = new Blob([response], {type: "text/plain;charset=utf-8"});
            saveAs(blob, 'bill_' + id + ".html");
        }, function(response) {});
    };


    $scope.downloadDetails = function(date) {
        billService.getDetails(date, function(response) {
            var blob = new Blob([response], {type: "application/pdf"});
            saveAs(blob, 'details_' + date + ".pdf");
        }, function(response) {

        });
    };

    billService.getBills(function(response) {
        $scope.bills = response;
    }, function (response) {
        alert('Something wrong: bill request failed');
    });

}]);

cabinetAppControllers.controller('ServiceController', ['$scope', 'phoneService', function($scope, phoneService) {
    
    phoneService.get(function (response) {
        $scope.services = response;
    }, function (response) {

    });
    
}]);

cabinetAppControllers.controller('NavController', ['$scope', 'userService', '$location', 'clientService', function($scope, userService, $location, clientService) {
    $scope.signout = function() {
        userService.signout();
        console.log('signout');
        $location.path('/signin');
    };

    clientService.getClient(function(response) {
        $scope.base_client = response.base_client;
    });

}]);

cabinetAppControllers.controller('MainController', ['$scope', 'userService', '$location', 'clientService', function($scope, userService, $location, clientService) {
    $scope.detailTypes = [{id: 1, title: 'исходящие местные вызовы'}, {id: 2, title: 'исходящие междугородние вызовы'}, {id: 4, title: 'все входящие вызовы'}];

    clientService.getClient(function(response) {
        $scope.base_client = response.base_client;
        angular.forEach($scope.detailTypes, function(detail) {
            detail.selected = detail.id == (detail.id & $scope.base_client.details_type);
        });
    }, function(response) {
        //alert(response);
    });


    $scope.btnShowA = function() {
        $scope.btnShow = true;
    };

    $scope.saveDetails = function() {
        $scope.btnShow = false;
        var value = 0;
        angular.forEach($scope.detailTypes, function(detail) {
            if(detail.selected)
                value += detail.id;
        });
        $scope.base_client.details_type = value;
        clientService.updateClient($scope.base_client, function(response) {
            console.log($scope.base_client);
        }, function(response) {

        });
    };

    $scope.formatContract = function(contract)
    {
        var item =  contract.contract_title.replace('&#8470;', 'N ');
        return item.charAt(0).toUpperCase() + item.slice(1);
    };

    $scope.selectedDetails = function(id, value) {
        return id & value;
    };

    // edit base_client email and contact phone
    $scope.editor = function(item, element) {
        item['editing_' + element] = true;
        item.oldValue = item[element];
    };

    $scope.doneEditing = function(item, element) {
        item['editing_' + element] = false;
        clientService.updateClient($scope.base_client, function(response) {
            console.log($scope.base_client);
            $scope.base_client['error_' + element] = false;
        }, function(response) {
            item[element] = item.oldValue;
            $scope.base_client['error_' + element] = true;
            //alert('Не удалось сохранить значение, проверьте правильность ввода данных');
        });
    };

    $scope.cancelEditing = function(item, element) {
        item['editing_' + element] = false;
        item[element] = item.oldValue;
    };

    if(!userService.isSignedIn()) {
        $location.path('signin');
        return;
    }

}]);

