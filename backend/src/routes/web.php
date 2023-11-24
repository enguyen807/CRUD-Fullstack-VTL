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

// $router->get('/', function () {
//     $data = array("a" => "Apple", "b" => "Ball", "c" => "Cat");
//     return response(json_encode($data), 200);
// });

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get("customers", ['uses' => 'CustomerController@index']);
    $router->post("customers", ['uses' => 'CustomerController@store']);
    $router->put("customers/{id}", ['uses' => 'CustomerController@update']);
    $router->delete("customers/{id}", ['uses' => 'CustomerController@destroy']);
});

