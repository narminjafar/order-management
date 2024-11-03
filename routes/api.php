<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api_logs']], function () {

    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth'], function () {

        require __DIR__ . '/auth.php';

        Route::get('orders', [OrderController::class, 'list']);
        Route::get('orders-by-user', [OrderController::class, 'listByUser']);

        Route::resource('orders', OrderController::class)->except(['index', 'create', 'edit']);

    });

});
