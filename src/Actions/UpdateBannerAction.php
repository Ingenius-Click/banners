<?php

namespace Ingenius\Banners\Actions;

use Illuminate\Support\Facades\DB;
use Ingenius\Banners\Models\Banner;

class UpdateBannerAction {

    public function handle(Banner $banner, array $data): Banner {

        DB::beginTransaction();

        try {

            // Extract content type from data
            $contentType = $data['banner_content_type'] ?? null;

            if(!$contentType) {
                throw new \Exception('Content type is required');
            }

            $banner->update([
                'name' => $data['name'] ?? $banner->name,
                'status' => $data['status'] ?? $banner->status,
                'priority' => $data['priority'] ?? $banner->priority,
                'starts_at' => $data['starts_at'] ?? $banner->starts_at,
                'ends_at' => $data['ends_at'] ?? $banner->ends_at,
                'metadata' => $data['metadata'] ?? $banner->metadata,
            ]);

            // Update banner content
            if($contentType) {
                // Get the active content
                $activeContent = $banner->contents()->where('is_active', true)->first();

                if ($activeContent) {
                    // Update existing content if it's the same type
                    if ($activeContent->content_type === $contentType) {
                        $activeContent->saveContent($data);
                    } else {
                        // Content type changed, deactivate old content and create new one
                        $activeContent->update(['is_active' => false]);
                        $banner->contents()->create([
                            'content_type' => $contentType,
                            'is_active' => true,
                            'version' => $banner->contents()->max('version') + 1,
                        ])->saveContent($data);
                    }
                } else {
                    // No active content, create new one
                    $banner->contents()->create([
                        'content_type' => $contentType,
                        'is_active' => true,
                        'version' => $banner->contents()->max('version') + 1,
                    ])->saveContent($data);
                }
            }

            if(isset($data['placements']) && is_array($data['placements'])) {
                $banner->placements()->delete();
                foreach($data['placements'] as $placementKey) {
                    $banner->placements()->create([
                        'placement_key' => $placementKey,
                    ]);
                }
            }

            if(isset($data['targets']) && is_array($data['targets'])) {
                $banner->targets()->delete();
                foreach($data['targets'] as $target) {
                    $banner->targets()->create([
                        'target_type' => $target['type'],
                        'target_data' => $target['data'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return $banner->refresh()->load(['contents', 'placements', 'targets']);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
}