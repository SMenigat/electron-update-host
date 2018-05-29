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

$router->post('/update/{version}', 'UpdateController@uploadUpdate');
$router->get('/update/{platform}/latest', 'UpdateController@downloadLatest');
$router->get('/check-update/{platform}/{version}', 'UpdateController@checkUpdate');
