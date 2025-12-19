<?php

namespace Ingenius\Banners\Actions;

use Exception;
use Illuminate\Support\Facades\DB;
use Ingenius\Banners\Models\Banner;
use Ingenius\Banners\Models\BannerContent;

class StoreBannerAction
{
    /**
     * Handle the banner creation with content
     *
     * @param array $data
     * @return Banner
     * @throws Exception
     */
    public function handle(array $data): Banner
    {
        DB::beginTransaction();

        try {
            // Extract content type from data
            $contentType = $data['banner_content_type'] ?? null;

            if (!$contentType) {
                throw new Exception('Content type is required');
            }

            // Create banner with main fields
            $banner = Banner::create([
                'name' => $data['name'],
                'status' => $data['status'] ?? 'draft',
                'priority' => $data['priority'] ?? 50,
                'starts_at' => $data['starts_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            // Create banner content
            if($contentType) {
                $bannerContent = $banner->contents()->create([
                    'content_type' => $contentType,
                    'is_active' => true,
                    'version' => 1,
                ]);

                // Save content data and media using the content type handler
                $bannerContent->saveContent($data);
            }

            // Handle placements if provided
            if (isset($data['placements']) && is_array($data['placements'])) {
                foreach ($data['placements'] as $placementKey) {
                    $banner->placements()->create([
                        'placement_key' => $placementKey,
                    ]);
                }
            }

            // Handle targets if provided (Phase 2)
            if (isset($data['targets']) && is_array($data['targets'])) {
                foreach ($data['targets'] as $target) {
                    $banner->targets()->create([
                        'target_type' => $target['type'],
                        'target_data' => $target['data'] ?? null,
                    ]);
                }
            }

            DB::commit();

            // Reload relationships
            return $banner->load(['contents', 'placements', 'targets']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}