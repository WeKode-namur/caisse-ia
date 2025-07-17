<?php

use App\Http\Controllers\Settings\AttributesController;
use App\Http\Controllers\Settings\CategoriesController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\UnknownItemsController;
use App\Http\Controllers\Settings\UpdatesController;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Settings\LogsController;
// use App\Http\Controllers\Settings\RolesController;

// use App\Http\Controllers\Settings\UsersController;


// Route d'accès refusé (en dehors du middleware settings.session)


// Routes AJAX (sans confirmation de mot de passe, mais avec vérification admin)
Route::prefix('settings')->name('settings.')->middleware(['auth:sanctum', 'verified', 'module.access'])->group(function () {
    Route::get('/no-access', function () {
        return view('panel.settings.no-access');
    })->name('no-access');
    // Routes AJAX pour les attributs
    Route::prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/stats', [AttributesController::class, 'getStats'])->name('stats');
        Route::get('/table', [AttributesController::class, 'getTableData'])->name('table');
        Route::get('/{attribute}/values-table', [AttributesController::class, 'ajaxTable'])->name('values.table');
        Route::get('/{attribute}/values-archives-table', [AttributesController::class, 'ajaxArchivesTable'])->name('values.archivesTable');
        Route::get('/{attribute}/values/{value}', [AttributesController::class, 'showValue'])->name('values.show');

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

    // Routes AJAX pour les catégories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/table', [CategoriesController::class, 'getTable'])->name('table');
        Route::get('/stats', [CategoriesController::class, 'getStats'])->name('stats');
        Route::get('/icons', [CategoriesController::class, 'getIcons'])->name('icons');
        Route::post('/', [CategoriesController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoriesController::class, 'update'])->name('update');
        Route::patch('/{category}/toggle', [CategoriesController::class, 'toggle'])->name('toggle');
        Route::delete('/{category}', [CategoriesController::class, 'destroy'])->name('destroy');

        // Routes pour les types
        Route::prefix('{category}/types')->name('types.')->group(function () {
            Route::get('/table', [CategoriesController::class, 'getTypesTable'])->name('table');
            Route::get('/stats', [CategoriesController::class, 'getTypesStats'])->name('stats');
            Route::post('/', [CategoriesController::class, 'storeType'])->name('store');
            Route::put('/{type}', [CategoriesController::class, 'updateType'])->name('update');
            Route::patch('/{type}/toggle', [CategoriesController::class, 'toggleType'])->name('toggle');
            Route::delete('/{type}', [CategoriesController::class, 'destroyType'])->name('destroy');
        });
    });

    // Actions pour les articles inconnus
    Route::prefix('unknown-items')->name('unknown-items.')->group(function () {
        Route::get('/table', [UnknownItemsController::class, 'getTableData'])->name('table');
        Route::get('/stats', [UnknownItemsController::class, 'getStats'])->name('stats');
        Route::get('/variants', [UnknownItemsController::class, 'getVariantsForRegularization'])->name('variants');
        Route::post('/search', [UnknownItemsController::class, 'searchArticles'])->name('search');
        Route::post('/{unknownItem}/regularize', [UnknownItemsController::class, 'regularize'])->name('regularize');
        Route::post('/{unknownItem}/mark-non-identifiable', [UnknownItemsController::class, 'markNonIdentifiable'])->name('mark-non-identifiable');
        Route::delete('/{unknownItem}', [UnknownItemsController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [UnknownItemsController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/report', [UnknownItemsController::class, 'generateReport'])->name('report');
    });

    // Routes AJAX pour les mises à jour
    Route::prefix('updates')->name('updates.')->group(function () {
        Route::get('/versions', [UpdatesController::class, 'getVersions'])->name('versions');
        Route::get('/search', [UpdatesController::class, 'searchVersions'])->name('search');
        Route::get('/content', [UpdatesController::class, 'getVersionContent'])->name('content');
        Route::get('/stats', [UpdatesController::class, 'getStats'])->name('stats');
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
    });

    // Gestion des catégories (vues principales uniquement)
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('index');
        Route::get('/create', [CategoriesController::class, 'create'])->name('create');
        Route::get('/{category}/edit', [CategoriesController::class, 'edit'])->name('edit');

        // Types des catégories (vues uniquement)
        Route::get('/{category}/types', [CategoriesController::class, 'types'])->name('types');
    });

    // Articles inconnus
    Route::prefix('unknown-items')->name('unknown-items.')->group(function () {
        Route::get('/', [UnknownItemsController::class, 'index'])->name('index');
        Route::get('/{unknownItem}', [UnknownItemsController::class, 'show'])->name('show');
    });

    // Historique des mises à jour
    Route::prefix('updates')->name('updates.')->group(function () {
        Route::get('/', [UpdatesController::class, 'index'])->name('index');
    });
});


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
