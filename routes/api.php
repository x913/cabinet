<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', ['middleware' => 'api.throttle', 'limit' => 100, 'expires' => 5], function (Router $api) {

    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
        $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');
        $api->get('token', 'App\\Api\\V1\\Controllers\\AuthController@token');

    });

    //$api->group(['middleware' => ['before' => 'jwt.auth', 'after' => 'jwt.refresh']], function(Router $api) {
    $api->group(['middleware' => ['jwt.auth']], function(Router $api) {
        $api->get('base_clients', 'App\\Api\\V1\\Controllers\\BaseClientController@index');
        $api->put('base_clients/{id}', 'App\\Api\\V1\\Controllers\\BaseClientController@update');


        $api->post('base_clients/email', 'App\\Api\\V1\\Controllers\\BaseClientController@addEmail');
        $api->delete('base_clients/email/{id}', 'App\\Api\\V1\\Controllers\\BaseClientController@removeEmail');



        $api->get('services', 'App\\Api\\V1\\Controllers\\ServiceController@index');
        $api->get('contracts', 'App\\Api\\V1\\Controllers\\ContractController@index');
        $api->get('fixed_fees', 'App\\Api\\V1\\Controllers\\FixedFeeController@index');
        $api->get('bills', 'App\\Api\\V1\\Controllers\\BillController@index');
        $api->get('payments', 'App\\Api\\V1\\Controllers\\PaymentController@index');


        $api->get('bills/{id}', 'App\\Api\\V1\\Controllers\\BillController@show');

        $api->get('details/{date}', 'App\\Api\\V1\\Controllers\\DetailController@show');
        $api->put('services/{id}', 'App\\Api\\V1\\Controllers\\ServiceController@update');

        $api->get('stz_departments', 'App\\Api\\V1\\Controllers\\StzDepartmentController@index');



        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to this item is only for authenticated user. Provide a token in your request!'
            ]);
        });

        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);
    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });
});
