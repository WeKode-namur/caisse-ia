<?php

use App\Http\Controllers\Transaction\{TicketController, FactureController};
use App\Http\Controllers\TransactionController;

Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');

Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/{id}', [TicketController::class, 'show'])->name('index');
    Route::post('/{id}/email', [TicketController::class, 'email'])->name('email');
});

Route::prefix('factures')->name('factures.')->group(function () {
    Route::get('/{id}', [FactureController::class, 'show'])->name('index');
});
