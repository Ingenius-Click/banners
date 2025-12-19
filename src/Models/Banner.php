<?php

namespace Ingenius\Banners\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
        'priority',
        'starts_at',
        'ends_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'priority' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'is_currently_active',
    ];

    /**
     * Get the contents for this banner
     */
    public function contents(): HasMany
    {
        return $this->hasMany(BannerContent::class);
    }

    /**
     * Get the active content for this banner
     */
    public function activeContent(): ?BannerContent
    {
        return $this->contents()->where('is_active', true)->first();
    }

    /**
     * Get the placements for this banner
     */
    public function placements(): HasMany
    {
        return $this->hasMany(BannerPlacement::class);
    }

    /**
     * Get the targets for this banner
     */
    public function targets(): HasMany
    {
        return $this->hasMany(BannerTarget::class);
    }

    /**
     * Scope a query to only include active banners
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include currently active banners
     * (active status AND within valid date range)
     */
    public function scopeCurrentlyActive($query)
    {
        $now = now();

        return $query->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            });
    }

    /**
     * Scope a query to filter by placement key
     */
    public function scopeForPlacement($query, string $placementKey)
    {
        return $query->whereHas('placements', function ($q) use ($placementKey) {
            $q->where('placement_key', $placementKey);
        });
    }

    /**
     * Scope a query to order by priority
     */
    public function scopeByPriority($query, string $direction = 'desc')
    {
        return $query->orderBy('priority', $direction);
    }

    /**
     * Get whether the banner is currently active
     * (has active status AND within the valid date range)
     */
    public function getIsCurrentlyActiveAttribute(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();

        // Check starts_at
        if ($this->starts_at && $now->lessThan($this->starts_at)) {
            return false;
        }

        // Check ends_at
        if ($this->ends_at && $now->greaterThan($this->ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * Check if banner is scheduled for future
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled'
            && $this->starts_at
            && now()->lessThan($this->starts_at);
    }

    /**
     * Check if banner has expired
     */
    public function isExpired(): bool
    {
        return $this->ends_at && now()->greaterThan($this->ends_at);
    }
}
