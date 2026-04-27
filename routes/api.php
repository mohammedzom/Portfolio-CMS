<?php

use App\Http\Controllers\Api\V1\AchievementController;
use App\Http\Controllers\Api\V1\Auth\Login;
use App\Http\Controllers\Api\V1\Auth\Logout;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EducationController;
use App\Http\Controllers\Api\V1\ExperienceController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\PortfolioController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\SiteSettingsController;
use App\Http\Controllers\Api\V1\SkillController;
use App\Http\Controllers\SkillCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('check-api-key')->group(function () {

    // Public Routes
    Route::post('/admin/login', Login::class)->name('login')->middleware('throttle:10,1');
    Route::post('/message', [MessageController::class, 'store'])->middleware('throttle:contact_form');
    Route::get('/portfolio', [PortfolioController::class, 'index'])->middleware('throttle:25,1');

    // Protected Routes
    Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
        Route::post('/logout', Logout::class);
        Route::get('/dashboard', DashboardController::class);

        Route::prefix('messages')->controller(MessageController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::patch('/{id}/read', 'markAsRead');
            Route::patch('/{id}/unread', 'markAsUnread');
            Route::delete('/{id}', 'destroy');
            Route::patch('/{id}/restore', 'restore');
            Route::delete('/{id}/force-delete', 'forceDelete');
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
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::patch('/{id}', 'update');
            Route::patch('/{id}/restore', 'restore');
            Route::delete('/{id}', 'destroy');
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

        Route::prefix('achievements')->controller(AchievementController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::patch('/{id}/restore', 'restore');
            Route::delete('/{id}/force-delete', 'forceDelete');
        });

        Route::prefix('education')->controller(EducationController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::patch('/{id}/restore', 'restore');
            Route::delete('/{id}/force-delete', 'forceDelete');
        });

        Route::prefix('skill-categories')->controller(SkillCategoryController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::patch('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::patch('/{id}/restore', 'restore');
            Route::delete('/{id}/force-delete', 'forceDelete');
        });

        Route::prefix('site-info')->controller(SiteSettingsController::class)->group(function () {
            Route::get('/', 'index');
            Route::patch('/', 'update');
        });
    });

});
