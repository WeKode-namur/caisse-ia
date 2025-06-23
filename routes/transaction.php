<?php

Route::get('/transactions', function () {
    return view('panel.transactions.view');
})->name('transactions');

Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/{id}', function ($id) {
        return view('panel.tickets.show', compact('id'));
    })->name('index');
});

Route::prefix('factures')->name('factures.')->group(function () {
    Route::get('/{id}', function ($id) {
        return view('panel.factures.show', compact('id'));
    })->name('index');
});
