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

const SILENCE = 'Hola querido padawan, no hay nada más para mostrar por aquí. Que la fuerza te acompañe.';

$router->group(['middleware' => 'auth'], function() use ($router){
    $router->post('topsecret',  
        ['uses' => 'MerlinController@getPositionAndMessage']);
    $router->post('topsecret_split/{satelliteName}',  
        ['uses' => 'MerlinController@setSplittedData']);
    $router->get('topsecret_split',
        ['uses' => 'MerlinController@getFromSplittedData']);
});

/*
// Si se intenta vía get, el resultado de cualquier request será el silencio
$router->get('/{any:.*}', function ($any) use ($router) {
    return SILENCE;
});
*/