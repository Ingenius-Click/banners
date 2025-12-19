<?php

namespace Ingenius\Banners\Actions;

use Illuminate\Support\Facades\DB;
use Ingenius\Banners\Models\Banner;

class DeleteBannerAction
{
    /**
     * Handle the banner deletion with all associated data
     *
     * @param Banner $banner
     * @return bool
     * @throws \Exception
     */
    public function handle(Banner $banner): bool
    {
        DB::beginTransaction();

        try {
            // Delete all media associated with banner contents
            // We need to load the contents first to access media
            $contents = $banner->contents()->get();

            foreach ($contents as $content) {
                // Clear all media for this content (deletes files from storage)
                $content->clearMediaCollection();
            }

            // Delete banner contents
            $banner->contents()->delete();

            // Delete banner placements
            $banner->placements()->delete();

            // Delete banner targets
            $banner->targets()->delete();

            // Soft delete the banner
            $banner->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
