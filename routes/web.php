<?php

use App\Http\Controllers\CoinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::inertia('/about', 'About');
Route::inertia('/contact', 'Contact');
Route::inertia('/casino', 'Roulette');
Route::inertia('/login', 'Login');
Route::inertia('/register', 'Register');

Route::post('/assign-coins', [CoinController::class, 'assignCoins']);
Route::get('/get-coins', [CoinController::class, 'getCoins']);



Route::get('/dashboard', [DashboardController::class, 'index'])
->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
