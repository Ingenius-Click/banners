<?php

namespace Ingenius\Banners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ingenius\Banners\ContentTypes\AbstractContentType;
use Ingenius\Banners\ContentTypes\ContentTypeFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BannerContent extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'banner_id',
        'content_type',
        'content_data',
        'version',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content_data' => 'array',
        'version' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the banner that owns this content
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        // Image Banner - Device-specific images
        $this->addMediaCollection('desktop_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);

        $this->addMediaCollection('tablet_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);

        $this->addMediaCollection('mobile_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);

        // Background image for rich content banners
        $this->addMediaCollection('background_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);

        // Multiple overlay images for rich content banners
        $this->addMediaCollection('overlay_images')
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('optimized')
                    ->format('webp')
                    ->quality(85)
                    ->nonQueued();
            });

        // Video file for video banners
        $this->addMediaCollection('video')
            ->singleFile()
            ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/ogg']);

        // Video poster image
        $this->addMediaCollection('video_poster')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']);
    }

    /**
     * Scope a query to only include active content
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by content type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('content_type', $type);
    }

    /**
     * Scope a query to order by version
     */
    public function scopeByVersion($query, string $direction = 'desc')
    {
        return $query->orderBy('version', $direction);
    }

    /**
     * Get the content type handler instance
     */
    public function getContentType(): AbstractContentType
    {
        return ContentTypeFactory::make($this);
    }

    /**
     * Render the banner content
     */
    public function render(array $options = []): string
    {
        return $this->getContentType()->render($options);
    }

    /**
     * Get editor data for admin panel
     */
    public function getEditorData(): array
    {
        return $this->getContentType()->getEditorData();
    }

    /**
     * Get validation rules for content data (instance method)
     */
    public function getRules(): array
    {
        return ContentTypeFactory::getRulesForType($this->content_type);
    }

    /**
     * Get validation rules for a specific content type (static method)
     * Use this when you don't have a BannerContent instance yet
     */
    public static function getRulesForType(string $contentType): array
    {
        return ContentTypeFactory::getRulesForType($contentType);
    }

    /**
     * Save content data and media
     */
    public function saveContent(array $data): self
    {
        return $this->getContentType()->save($data);
    }

    /**
     * Get form schema for this content type (instance method)
     */
    public function getFormSchema(): array
    {
        return ContentTypeFactory::getSchemaForType($this->content_type);
    }

    /**
     * Get form schema for a specific content type (static method)
     * Use this when you don't have a BannerContent instance yet
     */
    public static function getSchemaForType(string $contentType): array
    {
        return ContentTypeFactory::getSchemaForType($contentType);
    }
}
