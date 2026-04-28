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
use App\Http\Controllers\Api\V1\SkillCategoryController;
use App\Http\Controllers\Api\V1\SkillController;
use App\Http\Middleware\LogVisitor;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('check-api-key')->group(function () {

    // Public Routes
    Route::post('/admin/login', Login::class)->name('login')->middleware('throttle:10,1');
    Route::post('/message', [MessageController::class, 'store'])->middleware('throttle:contact_form');
    Route::get('/portfolio', [PortfolioController::class, 'index'])->middleware([LogVisitor::class, 'throttle:25,1']);
    Route::get('/projects/{slug}', [ProjectController::class, 'publicShow'])->middleware('throttle:25,1');

    // Protected Routes
    Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
        Route::post('/logout', Logout::class);
        Route::get('/dashboard', DashboardController::class);

        Route::apiResource('messages', MessageController::class)->only(['index', 'show', 'destroy']);
        Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead']);
        Route::patch('/messages/{message}/unread', [MessageController::class, 'markAsUnread']);
        Route::patch('/messages/{message}/restore', [MessageController::class, 'restore'])->withTrashed();
        Route::delete('/messages/{message}/force-delete', [MessageController::class, 'forceDelete'])->withTrashed();

        Route::apiResource('services', ServiceController::class);
        Route::patch('/services/{service}/restore', [ServiceController::class, 'restore'])->withTrashed();
        Route::delete('/services/{service}/force-delete', [ServiceController::class, 'forceDelete'])->withTrashed();

        Route::apiResource('experiences', ExperienceController::class);
        Route::patch('/experiences/{experience}/restore', [ExperienceController::class, 'restore'])->withTrashed();
        Route::delete('/experiences/{experience}/force-delete', [ExperienceController::class, 'forceDelete'])->withTrashed();

        Route::apiResource('skills', SkillController::class);
        Route::patch('/skills/{skill}/restore', [SkillController::class, 'restore'])->withTrashed();
        Route::delete('/skills/{skill}/force-delete', [SkillController::class, 'forceDelete'])->withTrashed();

        Route::apiResource('projects', ProjectController::class);
        Route::patch('/projects/{project}/restore', [ProjectController::class, 'restore'])->withTrashed();
        Route::delete('/projects/{project}/force-delete', [ProjectController::class, 'forceDelete'])->withTrashed();

        Route::apiResource('achievements', AchievementController::class);
        Route::patch('/achievements/{achievement}/restore', [AchievementController::class, 'restore'])->withTrashed();
        Route::delete('/achievements/{achievement}/force-delete', [AchievementController::class, 'forceDelete'])->withTrashed();

        Route::apiResource('education', EducationController::class);
        Route::patch('/education/{education}/restore', [EducationController::class, 'restore'])->withTrashed();
        Route::delete('/education/{education}/force-delete', [EducationController::class, 'forceDelete'])->withTrashed();

        Route::apiResource('skill-categories', SkillCategoryController::class);
        Route::patch('/skill-categories/{skill_category}/restore', [SkillCategoryController::class, 'restore'])->withTrashed();
        Route::delete('/skill-categories/{skill_category}/force-delete', [SkillCategoryController::class, 'forceDelete'])->withTrashed();

        Route::prefix('site-info')->controller(SiteSettingsController::class)->group(function () {
            Route::get('/', 'index');
            Route::patch('/', 'update');
        });
    });

});
