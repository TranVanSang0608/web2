<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Helpers\ViteAssetHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */    public function boot()
    {
        // Register a custom Blade directive for vite assets
        Blade::directive('viteAsset', function ($expression) {
            return "<?php echo \\App\\Helpers\\ViteAssetHelper::assetPath($expression); ?>";
        });
    }
}
