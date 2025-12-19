<?php

namespace Ingenius\Banners\ContentTypes;

use Illuminate\Http\UploadedFile;
use Ingenius\Banners\Models\BannerContent;
use Ingenius\Core\Support\Image;

class ImageBanner extends AbstractContentType
{
    /**
     * Render the banner content for display
     */
    public function render(array $options = []): string
    {
        $data = $this->model->content_data ?? [];

        // Get device-specific images or fall back to desktop
        $desktopMedia = $this->model->getFirstMedia('desktop_image');
        $tabletMedia = $this->model->getFirstMedia('tablet_image');
        $mobileMedia = $this->model->getFirstMedia('mobile_image');

        // If no images, return empty
        if (!$desktopMedia && !$tabletMedia && !$mobileMedia) {
            return '';
        }

        $html = '<div class="banner-image-wrapper">';

        // Wrap in link if URL is provided
        if (!empty($data['link_url'])) {
            $html .= sprintf(
                '<a href="%s" target="%s" class="banner-link" rel="%s">',
                e($data['link_url']),
                e($data['link_target'] ?? '_self'),
                ($data['link_target'] ?? '_self') === '_blank' ? 'noopener noreferrer' : ''
            );
        }

        // Responsive picture element
        $html .= '<picture>';

        // Mobile image (or fallback)
        if ($mobileMedia) {
            $html .= sprintf(
                '<source media="(max-width: 768px)" srcset="%s">',
                e($mobileMedia->getUrl())
            );
        } elseif ($desktopMedia) {
            $html .= sprintf(
                '<source media="(max-width: 768px)" srcset="%s">',
                e($desktopMedia->getUrl())
            );
        }

        // Tablet image (or fallback)
        if ($tabletMedia) {
            $html .= sprintf(
                '<source media="(max-width: 1024px)" srcset="%s">',
                e($tabletMedia->getUrl())
            );
        } elseif ($desktopMedia) {
            $html .= sprintf(
                '<source media="(max-width: 1024px)" srcset="%s">',
                e($desktopMedia->getUrl())
            );
        }

        // Desktop image (primary)
        $primaryImage = $desktopMedia ?? $tabletMedia ?? $mobileMedia;
        $html .= sprintf(
            '<img src="%s" alt="%s" class="banner-image" loading="lazy">',
            e($primaryImage->getUrl()),
            e($data['alt_text'] ?? '')
        );

        $html .= '</picture>';

        if (!empty($data['link_url'])) {
            $html .= '</a>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Get data for editing in admin panel
     */
    public function getEditorData(): array
    {
        $data = $this->model->content_data ?? [];

        $desktopMedia = $this->model->getFirstMedia('desktop_image');
        $tabletMedia = $this->model->getFirstMedia('tablet_image');
        $mobileMedia = $this->model->getFirstMedia('mobile_image');

        return [
            'desktop_image' => $desktopMedia ? new Image(
                $desktopMedia->id,
                $desktopMedia->getUrl(),
                $desktopMedia->getUrl(),
                $desktopMedia->getUrl(),
                $desktopMedia->mime_type,
                $desktopMedia->size
            ) : null,
            'tablet_image' => $tabletMedia ? new Image(
                $tabletMedia->id,
                $tabletMedia->getUrl(),
                $tabletMedia->getUrl(),
                $tabletMedia->getUrl(),
                $tabletMedia->mime_type,
                $tabletMedia->size
            ) : null,
            'mobile_image' => $mobileMedia ? new Image(
                $mobileMedia->id,
                $mobileMedia->getUrl(),
                $mobileMedia->getUrl(),
                $mobileMedia->getUrl(),
                $mobileMedia->mime_type,
                $mobileMedia->size
            ) : null,
            'alt_text' => $data['alt_text'] ?? null,
            'link_url' => $data['link_url'] ?? null,
            'link_target' => $data['link_target'] ?? '_self'
        ];
    }

    /**
     * Get validation rules for content data
     */
    public static function rules(): array
    {
        return [
            'desktop_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'tablet_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'alt_text' => 'nullable|string|max:255',
            'link_url' => 'nullable|url|max:500',
            'link_target' => 'nullable|in:_self,_blank',
        ];
    }

    public static function updateRules(): array {
        $rules = static::rules();

        return [
            ...$rules,
            'desktop_image' => 'nullable|' . str_replace('required|', '', $rules['desktop_image']),
            'desktop_image_deleted' => 'nullable|boolean',
            'tablet_image_deleted' => 'nullable|boolean',
            'mobile_image_deleted' => 'nullable|boolean',
        ];
    }

    /**
     * Save the content data and handle media uploads
     */
    public function save(array $data): BannerContent
    {
        // Save content_data (non-media fields)
        $contentData = [
            'alt_text' => $data['alt_text'] ?? null,
            'link_url' => $data['link_url'] ?? null,
            'link_target' => $data['link_target'] ?? '_self',
        ];

        $this->model->content_data = $contentData;
        $this->model->save();

        // Handle desktop image
        if (isset($data['desktop_image']) && $data['desktop_image'] instanceof UploadedFile) {
            // New image uploaded, replace existing
            $this->model->clearMediaCollection('desktop_image');
            $this->model->addMedia($data['desktop_image'])
                ->toMediaCollection('desktop_image');
        } elseif (!empty($data['desktop_image_deleted'])) {
            // User wants to delete the existing image
            $this->model->clearMediaCollection('desktop_image');
        }

        // Handle tablet image
        if (isset($data['tablet_image']) && $data['tablet_image'] instanceof UploadedFile) {
            // New image uploaded, replace existing
            $this->model->clearMediaCollection('tablet_image');
            $this->model->addMedia($data['tablet_image'])
                ->toMediaCollection('tablet_image');
        } elseif (!empty($data['tablet_image_deleted'])) {
            // User wants to delete the existing image
            $this->model->clearMediaCollection('tablet_image');
        }

        // Handle mobile image
        if (isset($data['mobile_image']) && $data['mobile_image'] instanceof UploadedFile) {
            // New image uploaded, replace existing
            $this->model->clearMediaCollection('mobile_image');
            $this->model->addMedia($data['mobile_image'])
                ->toMediaCollection('mobile_image');
        } elseif (!empty($data['mobile_image_deleted'])) {
            // User wants to delete the existing image
            $this->model->clearMediaCollection('mobile_image');
        }

        $this->model->refresh();

        return $this->model;
    }

    /**
     * Get schema for the content type (for dynamic forms)
     */
    public static function getSchema(): array
    {
        return [
            'type' => 'image',
            'label' => __('banners::messages.Image Banner'),
            'description' => __('banners::messages.A responsive banner with device-specific images and optional link'),
            'fields' => [
                [
                    'name' => 'desktop_image',
                    'type' => 'file',
                    'label' => __('banners::messages.Desktop Image'),
                    'required' => true,
                    'accept' => 'image/*',
                    'help' => __('banners::messages.Image for desktop devices (recommended: 1920x600px)'),
                ],
                [
                    'name' => 'tablet_image',
                    'type' => 'file',
                    'label' => __('banners::messages.Tablet Image'),
                    'required' => false,
                    'accept' => 'image/*',
                    'help' => __('banners::messages.Optional image for tablet devices (recommended: 1024x400px). Falls back to desktop if not provided.'),
                ],
                [
                    'name' => 'mobile_image',
                    'type' => 'file',
                    'label' => __('banners::messages.Mobile Image'),
                    'required' => false,
                    'accept' => 'image/*',
                    'help' => __('banners::messages.Optional image for mobile devices (recommended: 768x300px). Falls back to desktop if not provided.'),
                ],
                [
                    'name' => 'alt_text',
                    'type' => 'text',
                    'label' => __('banners::messages.Alt Text'),
                    'required' => false,
                    'placeholder' => __('banners::messages.Descriptive text for accessibility'),
                    'help' => __('banners::messages.Describe the image for screen readers and SEO'),
                ],
                [
                    'name' => 'link_url',
                    'type' => 'url',
                    'label' => __('banners::messages.Link URL'),
                    'required' => false,
                    'placeholder' => 'https://example.com',
                    'help' => __('banners::messages.Where should this banner link to?'),
                ],
                [
                    'name' => 'link_target',
                    'type' => 'select',
                    'label' => __('banners::messages.Link Target'),
                    'required' => false,
                    'options' => [
                        ['value' => '_self', 'label' => __('banners::messages.Same window')],
                        ['value' => '_blank', 'label' => __('banners::messages.New window')],
                    ],
                    'default' => '_self',
                    'help' => __('banners::messages.How should the link open?'),
                ],
            ],
        ];
    }

    /**
     * Get schema for updating the content type
     * Desktop image is optional when updating
     */
    public static function getUpdateSchema(): array
    {
        $schema = static::getSchema();

        // Make desktop_image optional for updates
        foreach ($schema['fields'] as &$field) {
            if ($field['name'] === 'desktop_image') {
                $field['required'] = false;
                $field['help'] = __('banners::messages.Optional: Upload only if you want to change the desktop image');
                break;
            }
        }

        return $schema;
    }
}
