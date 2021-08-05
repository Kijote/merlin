<?php

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

/*
$router->get('/', function () use ($router) {
    return $router->app->version();
});
*/

const SILENCE = 'Hola querido padawan, no hay nada más para mostrar por aquí. Que la fuerza te acompañe.';

$router->get('/{any:.*}', function ($any) use ($router) {
    return SILENCE;
});

$router->post('topsecret',         ['middleware' => 'auth', 'uses' => 'MerlinController@getPositionAndMessage']);
