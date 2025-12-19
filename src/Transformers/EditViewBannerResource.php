<?php

namespace Ingenius\Banners\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EditViewBannerResource extends JsonResource 
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $data = [
            'name' => $this->name,
            'status' => $this->status,
            'priority' => $this->priority,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'metadata' => $this->metadata
        ];

        $data['banner_content_type'] = $this->activeContent()?->content_type ?? null;

        if($this->placements()->exists()) {
            $data['placements'] = $this->placements->pluck('placement_key')->toArray();
        } else {
            $data['placements'] = [];
        }

        if($this->targets()->exists()) {
            $data['targets'] = $this->targets->pluck('target_key')->toArray();
        } else {
            $data['targets'] = [];
        }

        if($data['banner_content_type'] != null) {
            $data = [
                ... $data,
                ... $this->activeContent()?->getEditorData() ?? []
            ];
        }

        return $data;
    }
}