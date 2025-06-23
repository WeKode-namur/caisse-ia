<?php

use App\Http\Controllers\Transaction\TicketController;

Route::get('/transactions', function () {
    return view('panel.transactions.view');
})->name('transactions');

Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/{id}', [TicketController::class, 'show'])->name('index');
    Route::get('/{id}/print', [TicketController::class, 'print'])->name('print');
    Route::post('/{id}/email', [TicketController::class, 'email'])->name('email');
});

Route::prefix('factures')->name('factures.')->group(function () {
    Route::get('/{id}', function ($id) {
        return view('panel.factures.show', compact('id'));
    })->name('index');
});
