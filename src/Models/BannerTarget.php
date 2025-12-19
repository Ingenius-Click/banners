<?php

namespace Ingenius\Banners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannerTarget extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'banner_id',
        'target_type',
        'target_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_data' => 'array',
    ];

    /**
     * Get the banner that owns this target
     */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    /**
     * Scope a query to filter by target type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('target_type', $type);
    }
}
