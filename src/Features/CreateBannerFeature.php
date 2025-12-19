<?php

namespace Ingenius\Banners\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class CreateBannerFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'create-banner';
    }

    public function getName(): string
    {
        return 'Create banner';
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
