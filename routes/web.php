<?php

use App\Http\Controllers\Web\PortfolioController;
use App\Http\Middleware\LogVisitor;
use Illuminate\Support\Facades\Route;

Route::get('/', PortfolioController::class)
    ->middleware(LogVisitor::class)
    ->name('home');
