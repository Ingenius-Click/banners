<?php

namespace Ingenius\Banners\Enums;

enum ContentType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case RICH_CONTENT = 'rich_content';
    case HTML = 'html';

    /**
     * Get the string value of the enum
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Get all available content types as array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
