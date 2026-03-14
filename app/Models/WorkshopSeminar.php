<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkshopSeminar extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'description',
        'venue',
        'event_date',
        'event_time',
        'max_participants',
        'is_active',
        'is_featured',
        'sort_order',
        'sms_templates',
        'show_phone',
        'show_address',
        'show_notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'sms_templates' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'show_phone' => 'boolean',
            'show_address' => 'boolean',
            'show_notes' => 'boolean',
        ];
    }

    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class, 'workshop_seminar_venue')->withTimestamps();
    }

    public function trades(): BelongsToMany
    {
        return $this->belongsToMany(Trade::class, 'workshop_seminar_trade')->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(WorkshopEnrollment::class, 'workshop_seminar_id');
    }

    /** Display venue names: from venues relation if set, else legacy text */
    public function getVenueDisplayAttribute(): ?string
    {
        if ($this->relationLoaded('venues') && $this->venues->isNotEmpty()) {
            return $this->venues->pluck('name')->join(', ');
        }
        return $this->venue ?: null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
