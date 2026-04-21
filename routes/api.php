<?php

use App\Http\Controllers\Api\V1\Auth\Login;
use App\Http\Controllers\Api\V1\Auth\Logout;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ExperienceController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\PortfolioController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\SkillController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/login', Login::class)->name('login');

    Route::post('/message', [MessageController::class, 'store']);
    Route::get('/portfolio', [PortfolioController::class, 'index']);

    // Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', Logout::class)->name('logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('messages')->controller(MessageController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::patch('/{id}/read', 'markAsRead');
        Route::patch('/{id}/unread', 'markAsUnread');
        Route::delete('/{id}', 'destroy');
        Route::patch('/{id}/restore', 'restore');
        Route::delete('/{id}/force-delete', 'forceDelete');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('services')->controller(ServiceController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::patch('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
    Route::patch('/{id}/restore', 'restore');
    Route::delete('/{id}/force-delete', 'forceDelete');
});

Route::prefix('experiences')->controller(ExperienceController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::patch('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
    Route::patch('/{id}/restore', 'restore');
    Route::delete('/{id}/force-delete', 'forceDelete');
});

Route::prefix('skills')->controller(SkillController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::patch('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
    Route::patch('/{id}/restore', 'restore');
    Route::delete('/{id}/force-delete', 'forceDelete');
});

Route::prefix('projects')->controller(ProjectController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::patch('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
    Route::patch('/{id}/restore', 'restore');
    Route::delete('/{id}/force-delete', 'forceDelete');
});
// });
