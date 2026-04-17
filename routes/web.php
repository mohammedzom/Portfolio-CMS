<?php

use App\Http\Controllers\Admin\Login;
use App\Http\Controllers\Admin\Logout;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/login', 'admin.login')->name('login');
    Route::post('/login', Login::class)->name('login.store');
})->middleware('guest');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::post('/logout', Logout::class)->name('logout');

    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
    Route::get('/projects', fn () => view('admin.projects'))->name('projects');
    Route::get('/skills', fn () => view('admin.skills'))->name('skills');
    Route::get('/services', fn () => view('admin.services'))->name('services');
    Route::get('/messages', fn () => view('admin.messages'))->name('messages');
    Route::get('/settings', fn () => view('admin.settings'))->name('settings');
});
