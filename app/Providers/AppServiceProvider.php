<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
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
        // Rendre la fonction translateStatus disponible globalement dans les vues
        Blade::directive('translateStatus', function ($expression) {
            return "<?php echo App\\Helpers\\TransactionHelper::translateStatus($expression); ?>";
        });
    }
}
