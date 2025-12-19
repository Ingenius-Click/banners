<?php

namespace Ingenius\Banners\Enums;

enum BannerTarget: string
{
    case ALL = 'all';
    case SPECIFIC_PRODUCTS = 'specific_products';
    case PRODUCT_CATEGORIES = 'product_categories';
    case PRODUCT_BRANDS = 'product_brands';
    case USER_SEGMENTS = 'user_segments';
    case NEW_USERS = 'new_users';
    case RETURNING_USERS = 'returning_users';
    case GUEST_USERS = 'guest_users';
    case AUTHENTICATED_USERS = 'authenticated_users';
    case GEOGRAPHIC_LOCATION = 'geographic_location';
    case DEVICE_TYPE = 'device_type';
    case CUSTOM = 'custom';

    /**
     * Get the string value of the enum
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Get all available targets as array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for the target
     */
    public function label(): string
    {
        return match($this) {
            self::ALL => 'All Users',
            self::SPECIFIC_PRODUCTS => 'Specific Products',
            self::PRODUCT_CATEGORIES => 'Product Categories',
            self::PRODUCT_BRANDS => 'Product Brands',
            self::USER_SEGMENTS => 'User Segments',
            self::NEW_USERS => 'New Users',
            self::RETURNING_USERS => 'Returning Users',
            self::GUEST_USERS => 'Guest Users',
            self::AUTHENTICATED_USERS => 'Authenticated Users',
            self::GEOGRAPHIC_LOCATION => 'Geographic Location',
            self::DEVICE_TYPE => 'Device Type',
            self::CUSTOM => 'Custom Targeting',
        };
    }

    /**
     * Get description for the target
     */
    public function description(): string
    {
        return match($this) {
            self::ALL => 'Show banner to all visitors',
            self::SPECIFIC_PRODUCTS => 'Target users viewing specific products',
            self::PRODUCT_CATEGORIES => 'Target users browsing specific categories',
            self::PRODUCT_BRANDS => 'Target users interested in specific brands',
            self::USER_SEGMENTS => 'Target predefined user segments',
            self::NEW_USERS => 'Target first-time visitors',
            self::RETURNING_USERS => 'Target users who have visited before',
            self::GUEST_USERS => 'Target non-authenticated visitors',
            self::AUTHENTICATED_USERS => 'Target logged-in users',
            self::GEOGRAPHIC_LOCATION => 'Target users from specific locations',
            self::DEVICE_TYPE => 'Target specific device types (mobile, tablet, desktop)',
            self::CUSTOM => 'Define custom targeting rules',
        };
    }

    /**
     * Check if target type requires additional data
     */
    public function requiresData(): bool
    {
        return match($this) {
            self::ALL,
            self::NEW_USERS,
            self::RETURNING_USERS,
            self::GUEST_USERS,
            self::AUTHENTICATED_USERS => false,
            default => true,
        };
    }

    /**
     * Get all targets with their labels and descriptions
     */
    public static function toArrayWithLabels(): array
    {
        return array_map(
            fn(self $case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'description' => $case->description(),
                'requires_data' => $case->requiresData(),
            ],
            self::cases()
        );
    }
}
