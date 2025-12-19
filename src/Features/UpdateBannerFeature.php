<?php

namespace Ingenius\Banners\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class UpdateBannerFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'update-banner';
    }

    public function getName(): string
    {
        return 'Update banner';
    }

    public function getGroup(): string
    {
        return 'Banners';
    }

    public function getPackage(): string
    {
        return 'banners';
    }

    public function isBasic(): bool
    {
        return false;
    }
}
