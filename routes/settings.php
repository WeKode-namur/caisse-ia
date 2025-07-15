<?php

use App\Http\Controllers\Settings\AttributesController;
use App\Http\Controllers\Settings\SettingsController;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Settings\CategoriesController;
// use App\Http\Controllers\Settings\LogsController;
// use App\Http\Controllers\Settings\RolesController;

// use App\Http\Controllers\Settings\UpdatesController;
// use App\Http\Controllers\Settings\UsersController;

// Routes AJAX (sans confirmation de mot de passe, mais avec vérification admin)
Route::prefix('settings')->name('settings.')->group(function () {

    // Routes AJAX pour les attributs
    Route::prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/stats', [AttributesController::class, 'getStats'])->name('stats');
        Route::get('/{attribute}/values-table', [AttributesController::class, 'ajaxTable'])->name('values.table');
        Route::get('/{attribute}/values-archives-table', [AttributesController::class, 'ajaxArchivesTable'])->name('values.archivesTable');

        // Actions CRUD pour les attributs
        Route::post('/', [AttributesController::class, 'store'])->name('store');
        Route::put('/{attribute}', [AttributesController::class, 'update'])->name('update');
        Route::delete('/{attribute}', [AttributesController::class, 'destroy'])->name('destroy');
        Route::patch('/{attribute}/activate', [AttributesController::class, 'activate'])->name('activate');

        // Actions pour les valeurs d'attributs
        Route::post('/{attribute}/values', [AttributesController::class, 'storeValue'])->name('values.store');
        Route::put('/{attribute}/values/{value}', [AttributesController::class, 'updateValue'])->name('values.update');
        Route::delete('/{attribute}/values/{value}', [AttributesController::class, 'destroyValue'])->name('values.destroy');
        Route::patch('/{attribute}/values/{value}/activate', [AttributesController::class, 'activateValue'])->name('values.activate');
        Route::post('/{attribute}/values/order', [AttributesController::class, 'updateValuesOrder'])->name('values.updateOrder');
    });

    // Actions pour les articles zero-stock
    Route::prefix('zero-stock')->name('zero-stock.')->group(function () {
        Route::post('/bulk-update', [SettingsController::class, 'bulkUpdateZeroStock'])->name('bulk-update');
    });
});

// Routes principales (avec confirmation de mot de passe)
Route::prefix('settings')->name('settings.')->middleware(['auth:sanctum', 'verified', 'module.access', 'settings.session'])->group(function () {

    // Page d'accueil des paramètres
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::post('/', [SettingsController::class, 'confirmPassword'])->name('confirm-password');
    Route::post('/reset-session', [SettingsController::class, 'resetPasswordConfirmation'])->name('reset-session');

    // Gestion des attributs (vues principales uniquement)
    Route::prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/', [AttributesController::class, 'index'])->name('index');
        Route::get('/create', [AttributesController::class, 'create'])->name('create');
        Route::get('/{attribute}/edit', [AttributesController::class, 'edit'])->name('edit');

        // Valeurs des attributs (vues uniquement)
        Route::get('/{attribute}/values', [AttributesController::class, 'values'])->name('values');
        Route::get('/{attribute}/values/{value}', [AttributesController::class, 'showValue'])->name('values.show');
    });

    // Gestion des catégories (temporairement commenté)
    /*
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('index');
        Route::get('/create', [CategoriesController::class, 'create'])->name('create');
        Route::post('/', [CategoriesController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoriesController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoriesController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoriesController::class, 'destroy'])->name('destroy');

        // Sous-catégories
        Route::get('/{category}/subcategories', [CategoriesController::class, 'subcategories'])->name('subcategories');
        Route::post('/{category}/subcategories', [CategoriesController::class, 'storeSubcategory'])->name('subcategories.store');
        Route::put('/{category}/subcategories/{subcategory}', [CategoriesController::class, 'updateSubcategory'])->name('subcategories.update');
        Route::delete('/{category}/subcategories/{subcategory}', [CategoriesController::class, 'destroySubcategory'])->name('subcategories.destroy');
    });
    */

    // Gestion des utilisateurs (temporairement commenté)
    /*
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('index');
        Route::get('/create', [UsersController::class, 'create'])->name('create');
        Route::post('/', [UsersController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UsersController::class, 'update'])->name('update');
        Route::delete('/{user}', [UsersController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UsersController::class, 'toggleStatus'])->name('toggle-status');
    });
    */

    // Gestion des rôles (temporairement commenté)
    /*
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RolesController::class, 'index'])->name('index');
        Route::get('/create', [RolesController::class, 'create'])->name('create');
        Route::post('/', [RolesController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [RolesController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RolesController::class, 'update'])->name('update');
        Route::delete('/{role}', [RolesController::class, 'destroy'])->name('destroy');
    });
    */

    // Logs système (temporairement commenté)
    /*
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [LogsController::class, 'index'])->name('index');
        Route::get('/cash-register', [LogsController::class, 'cashRegister'])->name('cash-register');
        Route::get('/system', [LogsController::class, 'system'])->name('system');
        Route::get('/download/{type}', [LogsController::class, 'download'])->name('download');
    });
    */

    // Historique des mises à jour (temporairement commenté)
    /*
    Route::prefix('updates')->name('updates.')->group(function () {
        Route::get('/', [UpdatesController::class, 'index'])->name('index');
        Route::get('/{version}', [UpdatesController::class, 'show'])->name('show');
    });
    */

    // Articles Z (articles avec stock zéro)
    Route::prefix('zero-stock')->name('zero-stock.')->group(function () {
        Route::get('/', [SettingsController::class, 'zeroStock'])->name('index');
    });
});
