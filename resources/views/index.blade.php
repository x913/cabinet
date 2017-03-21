<!doctype html>
<html lang="en" ng-app="cabinetApp">
<head>
    <meta charset="UTF-8">

    <base href="/">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{--<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">--}}

    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap-spacelab.min.css">

    <script src="bower_components/angular/angular.js"></script>
   <script src="bower_components/lodash/dist/lodash.min.js"></script>
  {{--  <script src="bower_components/angular-route/angular-route.js"></script>--}}
    <script src="bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>

    <script src="bower_components/angular-local-storage/dist/angular-local-storage.js"></script>
    <script src="bower_components/restangular/dist/restangular.js"></script>
    <script src="bower_components/angular-filter/dist/angular-filter.js"></script>
    <script src="bower_components/file-saver.js/FileSaver.js"></script>
    <script src="bower_components/angular-recaptcha/release/angular-recaptcha.js"></script>

    <script src="js/app.js"></script>
    <script src="js/controllers.js"></script>
    <script src="js/services.js"></script>

    <title>Личный Кабинет - ЗАО "РАДИОТЕЛЕФОН"</title>

    <style>
        body {
            background: lightgray;
        }

    </style>


</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">

            </div>
        </div>
        <div ui-view>

        </div>
    </div>

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>