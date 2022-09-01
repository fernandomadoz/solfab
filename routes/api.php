<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



//APP
Route::post('SQL/{codigo_de_referencia}/{abm}/{token}', 'AppController@sql');
Route::post('BATCH/CLIENTE/{codigo_de_referencia}/{abm}/{token}', 'AppController@batchCliente');



Route::get('DASHBOARD/{app_id}/{nivel_de_acceso}/{token}', 'AppController@dashboard');
Route::get('CATEGORIAS/{app_id}/{nivel_de_acceso}/{token}', 'AppController@categorias');
Route::get('POSTEOS/{app_id}/{nivel_de_acceso}/{app_categoria_id}/{token}', 'AppController@posteos');