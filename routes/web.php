<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'search'], function () use ($router) {
    $router->get('provinces', [
        'uses' => 'MasterProvinceController@searchProvinces'
    ]);
    $router->get('cities', [
        'uses' => 'MasterCityController@searchCities'
    ]);
});
