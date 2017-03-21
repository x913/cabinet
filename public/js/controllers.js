/**
 * Created by k3kc on 14.02.2017.
 */
//var filter = angular.module('');

var cabinetAppControllers = angular.module('cabinetAppControllers', ['angular.filter', 'vcRecaptcha', 'ui.router']);

cabinetAppControllers.factory('util', function() {
    return {
        formatContract: function(contract) {
            if(contract == null)
                return;
            var item =  contract.contract_title.replace('&#8470;', 'N ');
            return item.charAt(0).toUpperCase() + item.slice(1);
        },
        periodName: function(date, mode = 1) {
            var months = [];
            if(mode == 1)
                months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
            else
                months = ['Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
            var items = date.split('.');
            if(date.match(/^\d{2}.\d{2}\.\d{4}$/))
                return months[ items[1] * 1 - 1 ] + ' ' + items[2];
            if(date.match(/^\d{2}\.\d{4}$/))
                return months[ items[0] * 1 - 1 ] + ' ' + items[1];
        }
    }
});

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

cabinetAppControllers.controller('PaymentController', ['$scope', 'paymentService', 'util', function($scope, paymentService, util) {

    $scope.periodName = function(date) {
        return util.periodName(date);
    };

    paymentService.getPayments(function(response) {
        $scope.payments = response;
    }, function(response) {

    });

}]);

cabinetAppControllers.controller('LoginController', ['$scope', '$location', 'userService', 'vcRecaptchaService', '$state', function($scope, $location, userService, vcRecaptchaService, $state) {
    
    $scope.credentials = {
        email: '',
        password: '',
        response: '',
        key: '6LfM5RgUAAAAANhFnq68JV6IPLupnWHT3OMrzfQg'
    };
    
    $scope.passwordHidden = true;
    $scope.passwordInputType = 'password';
    
    $scope.togglePasswordVisible = function() {
        $scope.passwordHidden = !$scope.passwordHidden;
        if($scope.passwordHidden)
            $scope.passwordInputType = 'password';
        else
            $scope.passwordInputType = 'text';
    };

    $scope.setResponse = function(response) {
        $scope.credentials.response = response;
    };

    $scope.setWidgetId = function (widgetId) {
        $scope.widgetId = widgetId;
    };

    $scope.cbExpiration = function() {
        $scope.credentials.response = null;
        vcRecaptchaService.reload($scope.widgetId);
    };

    $scope.signin = function() {
        $scope.signinProgress = true;
        userService.signin($scope.credentials,
            function(response) {    // onSuccess
                if(response.status == '200') {
                    //$location.path('/');
                    $state.go('profile');
                    $scope.signinProgress = false;
                } else {
                    $scope.error_notify = true;
                    $scope.credentials.response = null;
                    $scope.error_message = "Ошибка авторизации доступа: неверная пара/логин пароль";
                    $scope.signinProgress = false;
                    vcRecaptchaService.reload($scope.widgetId);
                }
            },
            function (response) {
                $scope.error_notify = true;
                $scope.credentials.response = null;
                vcRecaptchaService.reload($scope.widgetId);
                $scope.error_message = "Ошибка авторизации доступа: неверная пара/логин пароль";
                $scope.signinProgress = false;
            });
    };

    $scope.submitDisabled = function() {
        return !$scope.credentials.email || !$scope.credentials.password || !$scope.credentials.response || $scope.signinProgress;
    };

    $scope.error_notify = false;
    $scope.credentials.email = '';
    $scope.credentials.password = '';

    if(userService.isSignedIn()) {
        console.log('user is signed in');
        $state.go('profile');
    }

}]);

cabinetAppControllers.controller('SignupController', [ '$scope', '$location', 'userService', function($scope, $location, userService) {
/*    $scope.signup = function() {
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
    $scope.password = '';*/

    //if(userService.isSignedIn()) {
        //console.log('you are signed in');
        //$location.path('/');
    //}

}]);

cabinetAppControllers.controller('BillController', ['$scope', 'billService', 'util', function($scope, billService, util) {

    $scope.periodName = function(date) {
        return util.periodName(date);
    };

    $scope.formatContract = function(contract) {
        return util.formatContract(contract);
    };


    $scope.isEven = function(index) {
        return index % 3 == 0;
    };

    $scope.downloadBill = function(bill) {
        billService.getBillById(bill.bill_id, function(response) {
            var blob = new Blob([response], {type: "text/plain;charset=utf-8"});
            saveAs(blob, bill.bill_number + ".html");
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
        
    });

}]);

cabinetAppControllers.controller('ServiceController', ['$scope', 'phoneService', 'util', function($scope, phoneService, util) {

    $scope.selected = 'contract';

    $scope.selectedReport = function(report) {
        $scope.selected = report;
    };

    $scope.formatContract = function(contract) {
        return util.formatContract(contract);
    };

    phoneService.get(function (response) {
        $scope.data = response;
    }, function (response) {

    });

    $scope.expandContract = function(contract) {
        contract.expanded = !contract.expanded;
    };

}]);

cabinetAppControllers.controller('NavController', ['$scope', 'userService', '$state', 'clientService', function($scope, userService, $state, clientService) {

    $scope.signout = function() {
        userService.signout();
        $state.go('signin');
    };

    clientService.getClient(function(response) {
        $scope.base_client = response.base_client;
    }, function (response) {
        
    });

}]);

cabinetAppControllers.controller('ProfileController', ['$scope', 'userService', '$state', 'clientService', 'util', function($scope, userService, $state, clientService, util) {

    $scope.isSignedIn = userService.isSignedIn();

    if(!userService.isSignedIn()) {
        $state.go('signin');
        return;
    }

    clientService.getClient(function(response) {
        $scope.base_client = response.base_client;
        angular.forEach($scope.base_client.emails, function(email) {
            email.details = [{id: 1, title: 'исходящие местные вызовы', selected: false}, {id: 2, title: 'исходящие междугородние вызовы', selected: false}, {id: 4, title: 'все входящие вызовы', selected: false}];
            angular.forEach(email.details, function (detail) {
                detail.selected = detail.id == (detail.id & email.detail_type);
            });
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

        }, function(response) {

        });
    };

    $scope.formatContract = function(contract) {
        return util.formatContract(contract);
    };

    $scope.selectedDetails = function(id, value) {
        return id & value;
    };

 
    $scope.editEmailAddress = function(email) {
        email.$original = email.$original || angular.copy(email);
        email['editing'] = true;
    };

    $scope.saveEmailAddress = function(email) {
        clientService.updateClient($scope.base_client,
            function(response) {
                email['error_contact_email'] = false;
                email['editing'] = false;
            }, function(response) {
            //angular.copy(email.$original, email);
            //email['editing'] = false;
            email['error_contact_email'] = true;
        });
    };

    $scope.cancelEditEmailAddress = function(email)  {
        angular.copy(email.$original, email);
        email['editing'] = false;
    };

    $scope.emailRemoveHideError = function(email) {
        email['remove_error'] = false;
    };

    $scope.removeEmailAddress = function(email) {
        clientService.removeEmail(email,
            function(response) {
                email.removed = true;
            }, function(response){
                email.remove_error = true;

            }
        );
    };

    $scope.addEmail = function() {
        $scope.email = {
            client_id: $scope.base_client.client_id,
            visible: true,
            details: [{id: 1, title: 'исходящие местные вызовы', selected: false}, {id: 2, title: 'исходящие междугородние вызовы', selected: false}, {id: 4, title: 'все входящие вызовы', selected: false}]
        };

    };

    $scope.saveAddEmailAddress = function() {
        clientService.addEmail($scope.email, function(response) {
            $scope.email.error_contact_email = false;
            $scope.email.visible = false;
            $scope.email.id = response.data.id;
            $scope.base_client.emails.push($scope.email);
        }, function(response) {
            $scope.email.error_contact_email = true;
        });
    };

    $scope.cancelAddingEmailAddress = function() {
        $scope.email = {
          visible: false
        };
    };

    // edit base_client contact phone
    $scope.editor = function(item, element) {
        item['editing_' + element] = true;
        item.oldValue = item[element];
    };

    $scope.doneEditing = function(item, element) {
        item['editing_' + element] = false;
        clientService.updateClient($scope.base_client, function(response) {

            $scope.base_client['error_' + element] = false;
        }, function(response) {
            item[element] = item.oldValue;
            $scope.base_client['error_' + element] = true;
        });
    };

    $scope.cancelEditing = function(item, element) {
        item['editing_' + element] = false;
        item[element] = item.oldValue;
    };

}]);

