<?php

use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\Clients\CustomerController;
use App\Http\Controllers\Clients\CompanyController;
use App\Http\Controllers\Clients\AddressController;

Route::prefix('clients')->name('clients.')->group(function () {
    // Routes principales unifiées
    Route::get('', [ClientController::class, 'index'])->name('index');
    Route::get('create', [ClientController::class, 'create'])->name('create');
    Route::post('', [ClientController::class, 'store'])->name('store');

    // Routes pour les clients particuliers (détails, édition, suppression)
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::get('/search', [CustomerController::class, 'search'])->name('search');
    });

    // Routes pour les entreprises (détails, édition, suppression)
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/{company}', [CompanyController::class, 'show'])->name('show');
        Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
        Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
        Route::get('/search', [CompanyController::class, 'search'])->name('search');
    });

    // Routes pour les adresses
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/customers/{customer}/create', [AddressController::class, 'createForCustomer'])->name('create.customer');
        Route::get('/companies/{company}/create', [AddressController::class, 'createForCompany'])->name('create.company');
        Route::post('', [AddressController::class, 'store'])->name('store');
        Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [AddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/primary', [AddressController::class, 'setPrimary'])->name('primary');
    });
});
