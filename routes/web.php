<?php

use App\Http\Controllers\Admin\Login;
use App\Http\Controllers\Admin\Logout;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/login', 'admin.login')->name('login');
    Route::post('/login', Login::class)->name('login.store');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::post('/logout', Logout::class)->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('projects', ProjectController::class)->names('projects');
    Route::patch('projects/{project}/restore', [ProjectController::class, 'restore'])->name('projects.restore');

    Route::resource('skills', SkillController::class)->names('skills');
    Route::patch('skills/{skill}/restore', [SkillController::class, 'restore'])->name('skills.restore');

    Route::resource('services', ServiceController::class)->names('services');
    Route::patch('services/{service}/restore', [ServiceController::class, 'restore'])->name('services.restore');

    Route::resource('messages', MessageController::class)->names('messages');
    Route::patch('messages/{message}/restore', [MessageController::class, 'restore'])->name('messages.restore');

    Route::resource('settings', SiteSettingsController::class)->names('settings');

    Route::resource('experience', ExperienceController::class)->names('experience');
    Route::patch('experience/{experience}/restore', [ExperienceController::class, 'restore'])->name('experience.restore');
});
