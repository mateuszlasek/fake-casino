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
Route::get('/casino', [RouletteController::class, 'showRoulettePage'])->name('casino');
Route::inertia('/login', 'Login');
Route::inertia('/register', 'Register');

Route::post('/assign-balance', [BalanceController::class, 'assignBalance']);
Route::get('/get-balance', [BalanceController::class, 'getBalance']);

Route::post('place-bet', [RouletteController::class, 'placeBet']);
Route::post('spin-wheel', [RouletteController::class, 'spinWheel']);

Route::get('get-current-spin', [RouletteController::class, 'getCurrentSpin']);
Route::post('clear-spin', [RouletteController::class, 'clearSpin']);
Route::get('/get-history', [RouletteController::class, 'getHistory']);




Route::get('/dashboard', [DashboardController::class, 'index'])
->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
