<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('https://mohammedzomlot.dev');
});

Route::any('{any}', function () {
    return redirect('https://mohammedzomlot.dev');
})->where('any', '.*');
