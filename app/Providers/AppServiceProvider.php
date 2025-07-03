<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{Blade, Schema, View, Auth};
use App\Helpers\TransactionHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Rendre la fonction translateStatus disponible globalement dans les vues
        Blade::directive('translateStatus', function ($expression) {
            return "<?php echo App\\Helpers\\TransactionHelper::translateStatus($expression); ?>";
        });

        // Injection du flag de changelog dans le layout principal du panel
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();
            $currentVersion = config('custom.version.current');
            $checkFrom = config('custom.version.check_from');
            $showChangelog = $user
                && version_compare($currentVersion, $user->last_seen_version ?? '0.0.0', '>')
                && version_compare($currentVersion, $checkFrom, '>=');
            $view->with('showChangelog', $showChangelog);
            $view->with('changelogVersion', $currentVersion);
        });

        Blade::if('suppliersEnabled', function () {
            return config('custom.suppliers_enabled');
        });
    }
}
