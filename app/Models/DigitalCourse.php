<?php

namespace App\Models;

use App\Models\Concerns\HasYoutubeUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalCourse extends Model
{
    use HasYoutubeUrl;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'price',
        'compare_at_price',
        'thumbnail',
        'preview_youtube_url',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DigitalCourseCategory::class, 'category_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(DigitalCourseLesson::class)->orderBy('sort_order');
    }

    public function activeLessons(): HasMany
    {
        return $this->lessons()->where('is_active', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(DigitalCourseOrder::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(DigitalCourseEnrollment::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return str_starts_with($this->thumbnail, 'http')
                ? $this->thumbnail
                : asset('storage/' . $this->thumbnail);
        }

        if ($this->preview_youtube_url) {
            return $this->youtubeThumbnailFor($this->preview_youtube_url);
        }

        $firstLesson = $this->relationLoaded('activeLessons')
            ? $this->activeLessons->first()
            : $this->activeLessons()->first();

        if ($firstLesson) {
            return $firstLesson->thumbnail_url;
        }

        return '';
    }

    public function getPreviewEmbedUrlAttribute(): string
    {
        return $this->embedUrlFor($this->preview_youtube_url);
    }

    public function getPreviewYoutubeVideoIdAttribute(): ?string
    {
        return $this->extractYoutubeVideoId($this->preview_youtube_url);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
