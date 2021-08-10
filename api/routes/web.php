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

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->get('/{userId}', ['uses' => 'UserController@show', 'as' => 'user.show']);
    $router->post('/', ['uses' => 'UserController@create', 'as' => 'user.create']);
    $router->patch('/{userId}', ['uses' => 'UserController@update', 'as' => 'user.update']);
    $router->delete('/{userId}', ['uses' => 'UserController@delete', 'as' => 'user.delete']);

    $router->get('/{userId}/transactions', ['uses' => 'UserController@transactions', 'as' => 'user.transactions']);
    $router->get('/{userId}/wallet', ['uses' => 'UserController@wallet', 'as' => 'user.wallet']);
});

$router->post('transaction', ['uses' => 'TransactionController@create', 'as' => 'transaction.create']);

// Helpers
$router->get('/user/list/all', 'UserController@all');
