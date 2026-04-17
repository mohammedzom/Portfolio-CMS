<?php

use App\Http\Controllers\Admin\Login;
use App\Http\Controllers\Admin\Logout;
use App\Http\Controllers\DashboardController;
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
})->middleware('guest');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::post('/logout', Logout::class)->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('projects', ProjectController::class)->names('projects');
    Route::resource('skills', SkillController::class)->names('skills');
    Route::resource('services', ServiceController::class)->names('services');
    Route::resource('messages', MessageController::class)->names('messages');
    Route::resource('settings', SiteSettingsController::class)->names('settings');
});
