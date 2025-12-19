<?php

namespace Ingenius\Banners\ContentTypes;

use Ingenius\Banners\Models\BannerContent;

abstract class AbstractContentType
{
    public function __construct(
        protected BannerContent $model
    ) {}

    /**
     * Render the banner content for display
     *
     * @param array $options Additional rendering options
     * @return string HTML output
     */
    abstract public function render(array $options = []): string;

    /**
     * Get data for editing in admin panel
     *
     * @return array Editor-ready data structure
     */
    abstract public function getEditorData(): array;

    /**
     * Get validation rules for content data
     * Static method so it can be called without an instance
     *
     * @return array Validation rules
     */
    abstract public static function rules(): array;

    public static function updateRules(): array {
        return static::rules();
    }

    /**
     * Save the content data and handle media uploads
     *
     * @param array $data Request data
     * @return BannerContent
     */
    abstract public function save(array $data): BannerContent;

    /**
     * Get schema for the content type (for dynamic forms)
     * Static method so it can be called without an instance
     *
     * @return array Form schema definition
     */
    abstract public static function getSchema(): array;

    /**
     * Get schema for updating the content type
     * By default, returns the same schema as create
     * Override this method to provide different validation for updates
     *
     * @return array Form schema definition
     */
    public static function getUpdateSchema(): array
    {
        return static::getSchema();
    }

    /**
     * Build inline CSS styles from array
     *
     * @param array $styles
     * @return string
     */
    protected function buildInlineStyles(array $styles): string
    {
        return collect($styles)
            ->map(fn($value, $key) => "$key: $value")
            ->implode('; ');
    }

    /**
     * Get the model instance
     *
     * @return BannerContent
     */
    public function getModel(): BannerContent
    {
        return $this->model;
    }
}
