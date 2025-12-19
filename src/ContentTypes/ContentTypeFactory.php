<?php

namespace Ingenius\Banners\ContentTypes;

use Ingenius\Banners\Models\BannerContent;
use InvalidArgumentException;

class ContentTypeFactory
{
    /**
     * Registered content type handlers
     *
     * @var array<string, class-string<AbstractContentType>>
     */
    protected static array $contentTypes = [
        'image' => ImageBanner::class,
        // 'video' => VideoBanner::class,
        // 'rich_content' => RichBanner::class,
        // 'html' => HtmlBanner::class,
    ];

    /**
     * Create a content type instance for the given banner content
     *
     * @param BannerContent $content
     * @return AbstractContentType
     * @throws InvalidArgumentException
     */
    public static function make(BannerContent $content): AbstractContentType
    {
        $type = $content->content_type;

        if (!isset(self::$contentTypes[$type])) {
            throw new InvalidArgumentException("Unknown content type: {$type}");
        }

        $class = self::$contentTypes[$type];

        return new $class($content);
    }

    /**
     * Register a custom content type handler
     *
     * @param string $type
     * @param class-string<AbstractContentType> $class
     * @return void
     */
    public static function register(string $type, string $class): void
    {
        if (!is_subclass_of($class, AbstractContentType::class)) {
            throw new InvalidArgumentException(
                "Content type class must extend " . AbstractContentType::class
            );
        }

        self::$contentTypes[$type] = $class;
    }

    /**
     * Get all registered content types
     *
     * @return array<string, class-string<AbstractContentType>>
     */
    public static function getRegisteredTypes(): array
    {
        return self::$contentTypes;
    }

    /**
     * Check if a content type is registered
     *
     * @param string $type
     * @return bool
     */
    public static function isRegistered(string $type): bool
    {
        return isset(self::$contentTypes[$type]);
    }

    /**
     * Get validation rules for a specific content type
     *
     * @param string $type
     * @return array
     * @throws InvalidArgumentException
     */
    public static function getRulesForType(string $type, string $action = 'create'): array
    {
        if (!isset(self::$contentTypes[$type])) {
            throw new InvalidArgumentException("Unknown content type: {$type}");
        }

        $class = self::$contentTypes[$type];

        if($action == 'create') {
            return $class::rules();
        }

        return $class::updateRules();
    }

    /**
     * Get schema for a specific content type
     *
     * @param string $type
     * @param string $action
     * @return array
     * @throws InvalidArgumentException
     */
    public static function getSchemaForType(string $type, string $action = 'create'): array
    {
        if (!isset(self::$contentTypes[$type])) {
            throw new InvalidArgumentException("Unknown content type: {$type}");
        }

        $class = self::$contentTypes[$type];

        if ($action === 'edit' && method_exists($class, 'getUpdateSchema')) {
            return $class::getUpdateSchema();
        }

        return $class::getSchema();
    }

    /**
     * Get all available schemas
     *
     * @return array
     */
    public static function getAllSchemas(): array
    {
        $schemas = [];

        foreach (self::$contentTypes as $type => $class) {
            try {
                $schemas[$type] = self::getSchemaForType($type);
            } catch (\Exception $e) {
                // Skip if there's an error getting the schema
                continue;
            }
        }

        return $schemas;
    }

    /**
     * Get all available rules
     *
     * @return array
     */
    public static function getAllRules(): array
    {
        $rules = [];

        foreach (self::$contentTypes as $type => $class) {
            try {
                $rules[$type] = self::getRulesForType($type);
            } catch (\Exception $e) {
                // Skip if there's an error getting the rules
                continue;
            }
        }

        return $rules;
    }
}
