<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\RouletteController;
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

Route::post('/assign-balance', [BalanceController::class, 'assignBalance']);
Route::get('/get-balance', [BalanceController::class, 'getBalance']);

Route::post('place-bet', [RouletteController::class, 'placeBet']);
Route::get('spin-wheel', [RouletteController::class, 'spinWheel']);
Route::post('/check-bets', [RouletteController::class, 'checkBets']);




Route::get('/dashboard', [DashboardController::class, 'index'])
->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
