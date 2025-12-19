<?php

namespace Ingenius\Banners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannerPlacement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'banner_id',
        'placement_key',
    ];

    /**
     * Get the banner that owns this placement
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    /**
     * Scope a query to filter by placement key
     */
    public function scopeForKey($query, string $placementKey)
    {
        return $query->where('placement_key', $placementKey);
    }
}
