<?php

namespace Ingenius\Banners\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicViewBannerResource extends JsonResource {

    public function toArray(\Illuminate\Http\Request $request): array {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'priority' => $this->priority,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'metadata' => $this->metadata,
        ];

        $placementKeys = $this->placements->pluck('placement_key')->toArray();
        $data['placements'] = $placementKeys;

        $targetKeys = $this->targets->pluck('target_type')->toArray();
        $data['targets'] = $targetKeys;

        $content = $this->activeContent();
        if($content) {
            $data['content']['render'] = $content->render();
            $data['content']['data'] = $content->getEditorData();
        }

        return $data;
    }

}