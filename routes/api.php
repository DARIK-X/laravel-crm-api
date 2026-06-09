<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CategoriesController;
use App\Http\Controllers\api\AdvertsController;


Route::get('/categories', [CategoriesController::class, 'index']);


Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])
    ->namespace('App\Http\Controllers\api')
    ->prefix('user')
    ->group(function () {
        Route::get('/', 'UserController@getUser');
        Route::patch('/', 'UserController@updateUser');
        Route::get('/adverts', 'UserController@getAdverts');
});

Route::prefix('adverts')
    ->namespace('App\Http\Controllers\api\Advert')
    ->group(function () {
        Route::get('/', [AdvertsController::class, 'index']);
        Route::get('/{id}', 'ShowController@__invoke');

        //auth adverts
        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('/', 'StoreController@__invoke');
            Route::patch('/{id}', 'UpdateController@__invoke' );
            Route::delete('/{id}', 'DeleteController@__invoke' );
            Route::post('/{id}/update-status', 'UpdateStatusController@__invoke');
        });
    });





