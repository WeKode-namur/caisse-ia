<?php
Route::prefix('clients')->name('clients.')->group(function () {
    Route::get('', function () { return view('panel.clients.index'); } )->name('index');



    // A traiter
    Route::get('/create', function () { return ''; })->name('create');
    Route::get('/{id}', function ($id) { return ''; })->name('show');
    Route::get('/{id}/edit', function ($id) { return ''; })->name('edit');
});
