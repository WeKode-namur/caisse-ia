<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventory\FournisseurController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

if (config('custom.suppliers_enabled')) {
    Route::middleware(['auth:sanctum', 'module.access'])->prefix('fournisseurs')->name('fournisseurs.')->group(function () {
        Route::get('/search', [FournisseurController::class, 'search'])->name('search');
        Route::get('/', [FournisseurController::class, 'index'])->name('index');
        Route::post('/', [FournisseurController::class, 'store'])->name('store');
        Route::get('/{id}', [FournisseurController::class, 'show'])->name('show');
        Route::put('/{id}', [FournisseurController::class, 'update'])->name('update');
        Route::delete('/{id}', [FournisseurController::class, 'destroy'])->name('destroy');
    });
}
