<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobCircular extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'description',
        'requirements',
        'education_options',
        'experience_options',
        'deadline',
        'is_active',
        'is_featured',
        'sort_order',
        'sms_templates',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'sms_templates' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'education_options' => 'array',
            'experience_options' => 'array',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
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
