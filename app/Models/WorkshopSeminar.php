<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'venue_id',
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

    public function venueRelation(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function trades(): BelongsToMany
    {
        return $this->belongsToMany(Trade::class, 'workshop_seminar_trade')->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(WorkshopEnrollment::class, 'workshop_seminar_id');
    }

    /** Display venue name: from relation if set, else legacy text */
    public function getVenueDisplayAttribute(): ?string
    {
        if ($this->venue_id && $this->relationLoaded('venueRelation') && $this->venueRelation) {
            return $this->venueRelation->name;
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
