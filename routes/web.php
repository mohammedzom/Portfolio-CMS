<?php

use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Portfolio
|--------------------------------------------------------------------------
*/
Route::get('/', [PortfolioController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Admin Dashboard (Frontend-only stubs — add auth middleware when ready)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
    Route::get('/projects', fn () => view('admin.projects'))->name('projects');
    Route::get('/skills', fn () => view('admin.skills'))->name('skills');
    Route::get('/services', fn () => view('admin.services'))->name('services');
    Route::get('/messages', fn () => view('admin.messages'))->name('messages');
    Route::get('/settings', fn () => view('admin.settings'))->name('settings');
});
