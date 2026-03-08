<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(WorkshopEnrollment::class, 'workshop_seminar_id');
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
