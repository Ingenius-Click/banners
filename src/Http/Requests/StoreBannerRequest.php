<?php

namespace Ingenius\Banners\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ingenius\Banners\ContentTypes\ContentTypeFactory;
use Ingenius\Banners\Enums\BannerPlacement;
use Ingenius\Banners\Enums\BannerStatus;
use Ingenius\Banners\Enums\BannerTarget;

class StoreBannerRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array {

        $bannerStatuses = array_column(BannerStatus::cases(), 'value');
        $bannerAvailableContentTypes = array_keys(ContentTypeFactory::getRegisteredTypes());
        $placements = array_column(BannerPlacement::cases(), 'value');
        $targets = array_column(BannerTarget::cases(), 'value');

        $contentType = $this->input('banner_content_type', null);
        
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', $bannerStatuses),
            'priority' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'targets' => 'nullable|array',
            'targets.*' => 'string|in:'.implode(',', $targets),
            'placements' => 'nullable|array',
            'placements.*' => 'string|in:'.implode(',', $placements),
            'banner_content_type' => 'required|in:'.implode(',', $bannerAvailableContentTypes),
            ... $contentType ? $this->buildContentValidationRules($contentType) : []
        ];
    }

    protected function buildContentValidationRules(string $contentType): array
    {
        if(!$contentType) {
            return [];
        }

        $rules = ContentTypeFactory::getRulesForType($contentType);

        return $rules;
    }
}