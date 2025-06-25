<?php

use Illuminate\Support\Facades\{Route, Request};

// Routes protégées par Jetstream (auth + email vérifié)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'module.access',
])->group(function () {

    // 📊 Page principale - Dashboard
    Route::get('/', function () {
        return view('panel.dashboard.view');
    })->name('dashboard');

    // 💰 Module Caisse
    require __DIR__ . '/register.php';

    // 🧾 Transaction / Tickets & Factures
    require __DIR__ . '/transaction.php';

    // 📦 Inventaire
    require __DIR__ . '/inventory.php';

    // 👥 Clients
    Route::get('/clients', function () {
        return '';
    })->name('clients.index');

    Route::get('/clients/create', function () {
        return '';
    })->name('clients.create');

    Route::get('/clients/{id}', function ($id) {
        return '';
    })->name('clients.show');

    Route::get('/clients/{id}/edit', function ($id) {
        return '';
    })->name('clients.edit');

    // 📈 Statistiques
    Route::get('/statistics', function () {
        return '';
    })->name('statistics');

    // 🔒 Clôture journalière
    Route::get('/closure', function () {
        return '';
    })->name('closure');

    // 🌐 Sorties "Wix"
    Route::get('/wix', function () {
        return view('panel.wix.releases.view');
    })->name('wix');

    // 👤 Utilisateurs
    Route::get('/users', function () {
        return '';
    })->name('users');

    Route::get('/users/create', function () {
        return '';
    })->name('users.create');

    Route::get('/users/{id}', function ($id) {
        return '';
    })->name('users.show');

    Route::get('/users/{id}/edit', function ($id) {
        return '';
    })->name('users.edit');

    // ⚙️ Paramètres
    Route::get('/settings', function () {
        return '';
    })->name('settings.index');

    Route::get('/settings/roles', function () {
        return '';
    })->name('settings.roles');

    Route::get('/settings/user', function () {
        return '';
    })->name('settings.user');

    // 🆘 Support
    Route::get('/support', function () {
        return '';
    })->name('support');

    // 📋 Logs / Boîte noire
    Route::get('/logs', function () {
        return '';
    })->name('logs.index');

    // Composant
    // > Loading spinner
    Route::get('/api/loading-spinner', function() {
        $message = Request::get('message', 'Chargement...');
        $size = Request::get('size', 'medium');

        return view('components.loading-spinner', [
            'message' => $message,
            'size' => $size,
            'overlay' => false
        ])->render();
    })->name('loading-spinner');

    // Mise à jour de la version vue par l'utilisateur (changelog)
    Route::post('/user/seen-version', function () {
        $user = auth()->user();
        $version = request('version');
        if ($user && $version) {
            $user->last_seen_version = $version;
            $user->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    })->name('user.seen-version');
});
