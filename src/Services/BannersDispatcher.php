<?php

namespace Ingenius\Banners\Services;

use Illuminate\Support\Collection;
use Ingenius\Banners\Models\Banner;

class BannersDispatcher
{

    public function getActiveBannersByPosition(string $position): Collection
    {
        $banners = Banner::query()
                    ->select('banners.*')
                    ->join('banner_placements', 'banners.id', '=', 'banner_placements.banner_id')
                    ->where('banner_placements.placement_key', $position)
                    ->currentlyActive()
                    ->byPriority()
                    ->get();

        return $banners;
    }

    /**
     * Get all banners grouped by their placements
     *
     * @param array $filters Optional filters to apply to the query
     * @return array
     */
    public function getBannersGroupedByPlacement(array $filters = []): array
    {
        $query = Banner::with([
                    'placements',
                    'targets',
                    'contents.media'
                ])
            ->byPriority()
            ;

        // Apply filters if provided
        if (!empty($filters)) {
            \Ingenius\Core\Services\GenericTableHandler::applyFilters($filters, $query);
        }

        $banners = $query->get();

        // Group banners by placement
        $grouped = [];

        foreach ($banners as $banner) {
            $placements = $banner->placements;

            // If banner has no placements, add it to "unassigned" group
            if ($placements->isEmpty()) {
                if (!isset($grouped['unassigned'])) {
                    $grouped['unassigned'] = [
                        'placement_key' => 'unassigned',
                        'placement_name' => __('banners::messages.Unassigned'),
                        'banners' => []
                    ];
                }
                $grouped['unassigned']['banners'][] = $this->formatBanner($banner);
            } else {
                // Add banner to each of its placement groups
                foreach ($placements as $placement) {
                    $placementKey = $placement->placement_key;

                    if (!isset($grouped[$placementKey])) {
                        $placementEnum = \Ingenius\Banners\Enums\BannerPlacement::tryFrom($placementKey);

                        $grouped[$placementKey] = [
                            'placement_key' => $placementKey,
                            'placement_name' => $placementEnum?->label() ?? $placementKey,
                            'banners' => []
                        ];
                    }

                    $grouped[$placementKey]['banners'][] = $this->formatBanner($banner);
                }
            }
        }

        return array_values($grouped);
    }

    /**
     * Format banner data for response
     *
     * @param Banner $banner
     * @return array
     */
    private function formatBanner(Banner $banner): array
    {
        $activeContent = $banner->activeContent();

        $data = [
            'id' => $banner->id,
            'name' => $banner->name,
            'status' => $banner->status,
            'priority' => $banner->priority,
            'starts_at' => $banner->starts_at?->toIso8601String(),
            'ends_at' => $banner->ends_at?->toIso8601String(),
            'is_currently_active' => $banner->is_currently_active,
            'metadata' => $banner->metadata,
            'placements' => $banner->placements->pluck('placement_key')->toArray(),
            'targets' => $banner->targets->pluck('target_key')->toArray(),
        ];

        if ($activeContent) {
            $data['content'] = [
                'id' => $activeContent->id,
                'content_type' => $activeContent->content_type,
                'content_data' => $activeContent->content_data,
                'version' => $activeContent->version,
                'media' => $this->formatMedia($activeContent),
            ];
        }

        return $data;
    }

    /**
     * Format media collections for the content
     *
     * @param \Ingenius\Banners\Models\BannerContent $content
     * @return array
     */
    private function formatMedia($content): array
    {
        $media = [];

        // Image banner media
        if ($content->hasMedia('desktop_image')) {
            $media['desktop_image'] = generate_tenant_aware_image_url($content->getFirstMediaUrl('desktop_image'));
        }
        if ($content->hasMedia('tablet_image')) {
            $media['tablet_image'] = generate_tenant_aware_image_url($content->getFirstMediaUrl('tablet_image'));
        }
        if ($content->hasMedia('mobile_image')) {
            $media['mobile_image'] = generate_tenant_aware_image_url($content->getFirstMediaUrl('mobile_image'));
        }

        // Rich content banner media
        if ($content->hasMedia('background_image')) {
            $media['background_image'] = generate_tenant_aware_image_url($content->getFirstMediaUrl('background_image'));
        }
        if ($content->hasMedia('overlay_images')) {
            $media['overlay_images'] = $content->getMedia('overlay_images')->map(function ($item) {
                return [
                    'id' => $item->id,
                    'url' => generate_tenant_aware_image_url($item->getUrl()),
                    'optimized_url' => generate_tenant_aware_image_url($item->getUrl('optimized')),
                ];
            })->toArray();
        }

        // Video banner media
        if ($content->hasMedia('video')) {
            $media['video'] = generate_tenant_aware_image_url($content->getFirstMediaUrl('video'));
        }
        if ($content->hasMedia('video_poster')) {
            $media['video_poster'] = generate_tenant_aware_image_url($content->getFirstMediaUrl('video_poster'));
        }

        return $media;  
    }
}