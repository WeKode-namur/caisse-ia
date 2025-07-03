<?php

use App\Http\Controllers\{Api\InventoryApiController,
    Inventory\ArticleController,
    Inventory\CreationController,
    Inventory\DraftController,
    InventoryController};
use Illuminate\Http\Request;

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('', [InventoryController::class, 'index'])->name('index');
    Route::get('/table', [InventoryController::class, 'getArticlesTable'])->name('table');

    Route::prefix('create')->name('create.')->group(function () {
        Route::get('/', [DraftController::class, 'index'])->name('index'); // Redirection vers drafts

        Route::prefix('step')->name('step.')->group(function () {
            Route::get('/1/{draft?}', [CreationController::class, 'stepOne'])->name('one');
            Route::post('/1', [CreationController::class, 'storeStepOne'])->name('one.store');
            Route::put('/1', [CreationController::class, 'storeStepOne'])->name('one.update');
            Route::get('/1/{draft}/edit', [CreationController::class, 'stepOne'])->name('one.edit');

            Route::get('/2/{draft}', [CreationController::class, 'stepTwo'])->name('two');
            Route::post('/2/{draft}', [CreationController::class, 'storeStepTwo'])->name('two.store');
            Route::put('/2/{draft}', [CreationController::class, 'storeStepTwo'])->name('two.update');

            // API AJAX pour variants
            Route::prefix('2/{draft}/variants')->name('two.variants.')->group(function () {
                Route::get('/', [CreationController::class, 'getVariants'])->name('list');
                Route::post('/', [CreationController::class, 'storeVariant'])->name('store');
                Route::get('/{variant}', [CreationController::class, 'getVariant'])->name('show');
                Route::delete('/{variant}', [CreationController::class, 'deleteVariant'])->name('destroy');
                Route::post('/{variant}/image', [CreationController::class, 'uploadVariantImage'])->name('upload.image');
                Route::delete('/{variant}/image', [CreationController::class, 'uploadVariantImage'])->name('delete.image');
            });
        });
    });

    Route::prefix('drafts')->name('drafts.')->group(function () {
        Route::get('/', [DraftController::class, 'index'])->name('index');
        Route::get('/table', [DraftController::class, 'getDrafts'])->name('table');
        Route::delete('/{id}', [DraftController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/duplicate', [DraftController::class, 'duplicate'])->name('duplicate');
    });

    // === ROUTES LOAD STATS INV ===
    Route::get('/stats', [InventoryController::class, 'getInventoryStats'])->name('stats');

    Route::get('/{id}', [ArticleController::class, 'show'])->name('show');
    Route::get('/{id}/variants', [ArticleController::class, 'getVariants'])->name('variants');
    Route::get('/variants/{id}/history', [ArticleController::class, 'getVariantHistory'])->name('variant.history');
    Route::get('/{id}/movements', [ArticleController::class, 'getMovements'])->name('movements');
    Route::get('/variants/{id}', [ArticleController::class, 'getVariant'])->name('variant.show');
    Route::post('{article}/labels/print-preview', [ArticleController::class, 'printLabelsPreview'])->name('labels.print-preview');
    Route::get('/{article}/movements/history', [\App\Http\Controllers\Inventory\MovementHistoryController::class, 'index'])->name('movements.history');
    Route::get('/{article}/movements/history/table', [\App\Http\Controllers\Inventory\MovementHistoryController::class, 'table'])->name('movements.history.table');
    Route::post('/{article}/stock/adjust', [\App\Http\Controllers\Inventory\StockAdjustmentController::class, 'store'])->name('stock.adjust');
    Route::get('/{id}/edit', [\App\Http\Controllers\Inventory\ArticleEditController::class, 'edit'])->name('edit');
    Route::post('/{id}/edit', [\App\Http\Controllers\Inventory\ArticleEditController::class, 'update'])->name('edit.save');
});

Route::prefix('api')->name('api.')->group(function () {
    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::get('/categories/{categoryId}/types', [InventoryApiController::class, 'getTypes'])->name('types');
        Route::get('/types/{typeId}/subtypes', [InventoryApiController::class, 'getSubtypes'])->name('subtypes');
    });
    Route::prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/', [InventoryApiController::class, 'getAttributes'])->name('list');
        Route::get('/{id}/values', [InventoryApiController::class, 'getAttributeValues'])->name('values');
    });
    Route::get('/variants/check-barcode', [CreationController::class, 'checkBarcodeUnique'])->name('check.barcode.unique');
});
