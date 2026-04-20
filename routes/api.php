<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\V1\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/login', Login::class)->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', Logout::class)->name('logout');

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::prefix('messages')->controller(MessageController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{message}', 'show');
            Route::delete('/{message}', 'destroy');
            Route::patch('/{message}/read', 'markAsRead');
            Route::patch('/{message}/unread', 'markAsUnread');
            Route::patch('/{id}/restore', 'restore');
        });

    });

});
