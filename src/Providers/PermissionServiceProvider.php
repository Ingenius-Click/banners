<?php

namespace Ingenius\Banners\Providers;

use Illuminate\Support\ServiceProvider;
use Ingenius\Banners\Constants\BannersPermissions;
use Ingenius\Core\Support\PermissionsManager;

class PermissionServiceProvider extends ServiceProvider
{
    protected string $packageName = 'Banners';

    /**
     * Bootstrap services.
     */
    public function boot(PermissionsManager $permissionsManager): void
    {
        $this->registerPermissions($permissionsManager);
    }

    /**
     * Register permissions.
     */
    protected function registerPermissions(PermissionsManager $permissionsManager): void
    {
        $permissionsManager->register(
            BannersPermissions::BANNERS_VIEW,
            'View banners',
            $this->packageName,
            'tenant',
            'View banners',
            'Banners'
        );

        $permissionsManager->register(
            BannersPermissions::BANNERS_CREATE,
            'Create banners',
            $this->packageName,
            'tenant',
            'Create banners',
            'Banners'
        );

        $permissionsManager->register(
            BannersPermissions::BANNERS_EDIT,
            'Edit banners',
            $this->packageName,
            'tenant',
            'Edit banners',
            'Banners'
        );

        $permissionsManager->register(
            BannersPermissions::BANNERS_DELETE,
            'Delete banners',
            $this->packageName,
            'tenant',
            'Delete banners',
            'Banners'
        );
    }
}
