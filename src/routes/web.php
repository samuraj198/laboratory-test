<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use \App\Http\Controllers\ContactController;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::post('/contact/store', [ContactController::class, 'store'])
    ->name('contact.store')->middleware('throttle:30,1');
