<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [UserController::class, 'login'])
    ->name('login')
    ->middleware('guest:sanctum');

Route::get('/logout', [UserController::class, 'logout'])
    ->name('logout')
    ->middleware('auth:sanctum');

Route::post('user/update', [UserController::class, 'update'])
    ->middleware('auth:sanctum')
    ->name('user.update');

Route::group([
    'prefix' => 'post',
    'as' => 'post.'
], function () {
    Route::get('/', [PostController::class, 'index'])
        ->name('index');

    Route::get('/{post}', [PostController::class, 'show'])
        ->whereNumber('post')
        ->name('show');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/store', [PostController::class, 'store'])
            ->name('store');

        Route::post('/{post}/update', [PostController::class, 'update'])
            ->whereNumber('post')
            ->name('update');

        Route::post('/{post}/destroy', [PostController::class, 'destroy'])
            ->whereNumber('post')
            ->name('destroy');
    });
});
