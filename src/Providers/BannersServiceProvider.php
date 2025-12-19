<?php

namespace Ingenius\Banners\Providers;

use Illuminate\Support\ServiceProvider;
use Ingenius\Banners\Features\CreateBannerFeature;
use Ingenius\Banners\Features\DeleteBannerFeature;
use Ingenius\Banners\Features\DisplayBannersFeature;
use Ingenius\Banners\Features\ListBannersFeature;
use Ingenius\Banners\Features\UpdateBannerFeature;
use Ingenius\Banners\Features\ViewBannerFeature;
use Ingenius\Core\Services\FeatureManager;
use Ingenius\Core\Traits\RegistersConfigurations;
use Ingenius\Core\Traits\RegistersMigrations;

class BannersServiceProvider extends ServiceProvider
{
    use RegistersMigrations, RegistersConfigurations;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/banners.php', 'banners');

        // Register the route service provider
        $this->app->register(RouteServiceProvider::class);

        // Register the permission service provider
        $this->app->register(PermissionServiceProvider::class);

        // Register features
        $this->app->afterResolving(FeatureManager::class, function (FeatureManager $manager) {
            $manager->register(new DisplayBannersFeature());
            $manager->register(new ListBannersFeature());
            $manager->register(new ViewBannerFeature());
            $manager->register(new CreateBannerFeature());
            $manager->register(new UpdateBannerFeature());
            $manager->register(new DeleteBannerFeature());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register configuration with the registry
        $this->registerConfig(__DIR__.'/../../config/banners.php', 'banners', 'banners');

        // Register migrations with the registry
        $this->registerMigrations(__DIR__.'/../../database/migrations', 'banners');

        // Check if there's a tenant migrations directory and register it
        $tenantMigrationsPath = __DIR__.'/../../database/migrations/tenant';
        if (is_dir($tenantMigrationsPath)) {
            $this->registerTenantMigrations($tenantMigrationsPath, 'banners');
        }
        
        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'banners');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'banners');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        
        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/banners.php' => config_path('banners.php'),
        ], 'banners-config');

        // Publish translations
        $this->publishes([
            __DIR__.'/../../lang' => $this->app->langPath('vendor/banners'),
        ], 'banners-lang');

        // Publish views
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/banners'),
        ], 'banners-views');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'banners-migrations');
    }
}