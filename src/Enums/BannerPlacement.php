<?php

namespace Ingenius\Banners\Enums;

enum BannerPlacement: string
{
    case HOME_HERO = 'home_hero';
    case HOME_TOP = 'home_top';
    case HOME_MIDDLE = 'home_middle';
    case HOME_BOTTOM = 'home_bottom';
    case CATEGORY_TOP = 'category_top';
    case CATEGORY_SIDEBAR = 'category_sidebar';
    case PRODUCT_PAGE_TOP = 'product_page_top';
    case PRODUCT_PAGE_BOTTOM = 'product_page_bottom';
    case CART_TOP = 'cart_top';
    case CART_SIDEBAR = 'cart_sidebar';
    case CHECKOUT_TOP = 'checkout_top';
    case ACCOUNT_DASHBOARD = 'account_dashboard';
    case FOOTER = 'footer';
    case POPUP = 'popup';

    /**
     * Get the string value of the enum
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Get all available placements as array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for the placement
     */
    public function label(): string
    {
        return match($this) {
            self::HOME_HERO => __('banners::messages.Home - Hero Banner'),
            self::HOME_TOP => __('banners::messages.Home - Top Section'),
            self::HOME_MIDDLE => __('banners::messages.Home - Middle Section'),
            self::HOME_BOTTOM => __('banners::messages.Home - Bottom Section'),
            self::CATEGORY_TOP => __('banners::messages.Category - Top'),
            self::CATEGORY_SIDEBAR => __('banners::messages.Category - Sidebar'),
            self::PRODUCT_PAGE_TOP => __('banners::messages.Product Page - Top'),
            self::PRODUCT_PAGE_BOTTOM => __('banners::messages.Product Page - Bottom'),
            self::CART_TOP => __('banners::messages.Cart - Top'),
            self::CART_SIDEBAR => __('banners::messages.Cart - Sidebar'),
            self::CHECKOUT_TOP => __('banners::messages.Checkout - Top'),
            self::ACCOUNT_DASHBOARD => __('banners::messages.Account Dashboard'),
            self::FOOTER => __('banners::messages.Footer'),
            self::POPUP => __('banners::messages.Popup Modal'),
        };
    }

    /**
     * Get all placements with their labels
     */
    public static function toArrayWithLabels(): array
    {
        return array_map(
            fn(self $case) => [
                'value' => $case->value,
                'label' => $case->label(),
            ],
            self::cases()
        );
    }
}
