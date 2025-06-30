<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Register\{CartController, PaymentController, ProductController, TransactionController};

Route::prefix('register')->name('register.')->group(function () {
    Route::get('/', [RegisterController::class, 'index'])->name('index');
    Route::post('/switch-cash-register', [RegisterController::class, 'switchCashRegister'])->name('switch-cash-register');
    Route::get('/session-info', [RegisterController::class, 'getSessionInfo'])->name('session-info');
    Route::get('/pending-sessions', [RegisterController::class, 'getPendingSessions'])->name('pending-sessions');
    Route::post('/restore-session/{sessionId}', [RegisterController::class, 'restoreSession'])->name('restore-session');

    Route::prefix('partials')->name('partials.')->group(function () {

        // === PRODUITS ===
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/search', [ProductController::class, 'search'])->name('search');
            Route::get('/by-category/{category}', [ProductController::class, 'byCategory'])->name('by-category');
            Route::get('/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('barcode');
            Route::get('/article/{articleId}/variants', [ProductController::class, 'getArticleVariants'])->name('article-variants');
            Route::get('/{variant}/variants', [ProductController::class, 'getVariants'])->name('variants');
        });

        // === PANIER ===
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add', [CartController::class, 'addItem'])->name('add-item');
            Route::put('/update/{itemId}', [CartController::class, 'updateItem'])->name('update-item');
            Route::delete('/remove/{itemId}', [CartController::class, 'removeItem'])->name('remove-item');
            Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
            Route::get('/totals', [CartController::class, 'getTotals'])->name('totals');
            Route::post('/discount/manual', [CartController::class, 'applyCustomDiscount'])->name('discount.manual');
            Route::post('/add-temporary', [CartController::class, 'addTemporaryItem'])->name('add-temporary-item');
        });

        // === REMISES ===
        Route::prefix('discounts')->name('discounts.')->group(function () {
            Route::get('/', [CartController::class, 'getAvailableDiscounts'])->name('index');
            Route::post('/apply', [CartController::class, 'applyDiscount'])->name('apply');
            Route::delete('/remove/{discountId}', [CartController::class, 'removeDiscount'])->name('remove');
            Route::post('/manual', [CartController::class, 'applyManualDiscount'])->name('manual');
        });

        // === CLIENTS ===
        if (config('app.register_customer_management', false)) {
            Route::prefix('customers')->name('customers.')->group(function () {
                Route::post('/select', [CartController::class, 'selectCustomer'])->name('select');
                Route::delete('/remove', [CartController::class, 'removeCustomer'])->name('remove');
                Route::get('/show', [CartController::class, 'showCustomer'])->name('show');
            });
        }

        // === CARTES CADEAUX ===
        Route::prefix('gift-cards')->name('gift-cards.')->group(function () {
            Route::get('/search/{code}', [CartController::class, 'findGiftCard'])->name('search');
            Route::post('/use', [CartController::class, 'useGiftCard'])->name('use');
            Route::post('/create', [CartController::class, 'createGiftCard'])->name('create');
        });

        // === PAIEMENT ===
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/methods', [PaymentController::class, 'getMethods'])->name('methods');
            Route::post('/process', [PaymentController::class, 'processPayment'])->name('process');
            Route::post('/finalize', [PaymentController::class, 'finalizeSale'])->name('finalize');
            Route::post('/calculate-change', [PaymentController::class, 'calculateChange'])->name('calculate-change');
            Route::post('/refund', [PaymentController::class, 'processRefund'])->name('refund');
        });

        // === TRANSACTIONS ===
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::post('/create', [TransactionController::class, 'create'])->name('create');
            Route::post('/create-from-cart', [TransactionController::class, 'createFromCart'])->name('create-from-cart');
            Route::get('/{transaction}/print', [TransactionController::class, 'print'])->name('print');
            Route::post('/{transaction}/void', [TransactionController::class, 'void'])->name('void');
            Route::post('/wix-release', [TransactionController::class, 'wixRelease'])->name('wix-release');
        });

        // === RETOURS ===
        Route::prefix('returns')->name('returns.')->group(function () {
            Route::get('/search/{transactionNumber}', [TransactionController::class, 'findTransaction'])->name('search');
            Route::post('/process', [TransactionController::class, 'processReturn'])->name('process');
        });
    });
});
