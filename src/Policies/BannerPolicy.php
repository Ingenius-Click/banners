<?php

namespace Ingenius\Banners\Policies;

use Ingenius\Banners\Constants\BannersPermissions;
use Ingenius\Banners\Models\Banner;

class BannerPolicy
{
    /**
     * Determine whether the user can view any banners.
     */
    public function viewAny($user): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(BannersPermissions::BANNERS_VIEW);
        }

        return false;
    }

    /**
     * Determine whether the user can view the banner.
     */
    public function view($user, Banner $banner): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(BannersPermissions::BANNERS_VIEW);
        }

        return false;
    }

    /**
     * Determine whether the user can create banners.
     */
    public function create($user): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(BannersPermissions::BANNERS_CREATE);
        }

        return false;
    }

    /**
     * Determine whether the user can update the banner.
     */
    public function update($user, Banner $banner): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(BannersPermissions::BANNERS_EDIT);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the banner.
     */
    public function delete($user, Banner $banner): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(BannersPermissions::BANNERS_DELETE);
        }

        return false;
    }
}
