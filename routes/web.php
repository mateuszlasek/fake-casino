<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\BlackjackController;
use App\Http\Controllers\RouletteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SlotController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');
Route::inertia('/about', 'About')->name('about');
Route::inertia('/casino', 'Casino')->name('casino');
Route::inertia('/contact', 'Contact')->name('contact');
Route::inertia('/login', 'Login')->name('login');
Route::inertia('/register', 'Register')->name('register');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/slots', [SlotController::class, 'index'])->name('slots');
    Route::post('/spin', [SlotController::class, 'spin'])->name('slots.spin');

    Route::prefix('balance')->group(function () {
        Route::post('/assign', [BalanceController::class, 'assignBalance'])->name('balance.assign');
        Route::get('/current', [BalanceController::class, 'getBalance'])->name('balance.get');
    });

    Route::prefix('roulette')->group(function () {
        Route::get('/', [RouletteController::class, 'showRoulettePage'])->name('roulette');
        Route::post('/place-bet', [RouletteController::class, 'placeBet'])->name('roulette.placeBet');
        Route::post('/spin-wheel', [RouletteController::class, 'spinWheel'])->name('roulette.spinWheel');
        Route::get('/current-spin', [RouletteController::class, 'getCurrentSpin'])->name('roulette.currentSpin');
        Route::post('/clear-spin', [RouletteController::class, 'clearSpin'])->name('roulette.clearSpin');
        Route::get('/history', [RouletteController::class, 'getHistory'])->name('roulette.history');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('blackjack')->group(function () {
        Route::get('/', function () {
            return inertia('Blackjack');
        })->name('blackjack');

        Route::post('/start', [BlackjackController::class, 'startGame'])->name('blackjack.start');
        Route::post('/hit', [BlackjackController::class, 'hit'])->name('blackjack.hit');
        Route::post('/stand', [BlackjackController::class, 'stand'])->name('blackjack.stand');
    });
});

require __DIR__.'/auth.php';
