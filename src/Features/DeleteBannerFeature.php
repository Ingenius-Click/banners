<?php

namespace Ingenius\Banners\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class DeleteBannerFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'delete-banner';
    }

    public function getName(): string
    {
        return 'Delete banner';
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
