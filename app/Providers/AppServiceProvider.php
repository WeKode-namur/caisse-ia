<?php

namespace App\Providers;

use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\{Auth, Blade, Schema, View};
use Illuminate\Support\ServiceProvider;

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

        // Rendre le helper SettingsHelper disponible globalement
        View::share('settings', new class {
            public function get($key, $default = null)
            {
                return SettingsHelper::get($key, $default);
            }

            public function getDefaultTva()
            {
                return SettingsHelper::getDefaultTva();
            }

            public function isCustomerManagementEnabled()
            {
                return SettingsHelper::isCustomerManagementEnabled();
            }

            public function isSuppliersEnabled()
            {
                return SettingsHelper::isSuppliersEnabled();
            }

            public function isBarcodeGeneratorEnabled()
            {
                return SettingsHelper::isBarcodeGeneratorEnabled();
            }

            public function getArticleSeuil()
            {
                return SettingsHelper::getArticleSeuil();
            }
        });
    }
}
