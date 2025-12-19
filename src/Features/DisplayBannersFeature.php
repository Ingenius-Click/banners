<?php

namespace Ingenius\Banners\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class DisplayBannersFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'display-banners';
    }

    public function getName(): string
    {
        return 'Display banners';
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
