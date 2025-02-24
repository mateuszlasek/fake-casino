<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\BlackjackController;
use App\Http\Controllers\RouletteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SlotController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::inertia('/about', 'About');
Route::inertia('/casino', 'Casino');
Route::inertia('/contact', 'Contact');
Route::get('/roulette', [RouletteController::class, 'showRoulettePage'])->name('roulette');
Route::inertia('/login', 'Login');
Route::inertia('/register', 'Register');

Route::post('/assign-balance', [BalanceController::class, 'assignBalance']);
Route::get('/get-balance', [BalanceController::class, 'getBalance']);

Route::post('place-bet', [RouletteController::class, 'placeBet']);
Route::post('spin-wheel', [RouletteController::class, 'spinWheel']);

Route::get('get-current-spin', [RouletteController::class, 'getCurrentSpin']);
Route::post('clear-spin', [RouletteController::class, 'clearSpin']);
Route::get('/get-history', [RouletteController::class, 'getHistory']);


Route::middleware('auth')->group(function () {
    Route::get('/slots', [SlotController::class, 'index'])->name('slots');
    Route::post('/spin', [SlotController::class, 'spin']);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/blackjack', function () {
    return Inertia::render('Blackjack');
})->middleware(['auth', 'verified']);

Route::prefix('blackjack')->group(function () {
    Route::post('/start', [BlackjackController::class, 'startGame']);
    Route::post('/hit', [BlackjackController::class, 'hit']);
    Route::post('/stand', [BlackjackController::class, 'stand']);
});

require __DIR__.'/auth.php';
