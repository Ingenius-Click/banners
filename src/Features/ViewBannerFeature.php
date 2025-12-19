<?php

namespace Ingenius\Banners\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ViewBannerFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'view-banner';
    }

    public function getName(): string
    {
        return 'View banner';
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
