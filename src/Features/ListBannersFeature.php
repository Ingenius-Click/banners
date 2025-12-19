<?php

namespace Ingenius\Banners\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ListBannersFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'list-banners';
    }

    public function getName(): string
    {
        return 'List banners';
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
