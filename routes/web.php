<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// user
Route::post('/user-registration', [UserController::class, 'UserRegistration']);

Route::post('/user-login', [UserController::class, 'UserLogin']);
Route::post('/otp', [UserController::class, 'OTPCode']);

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
