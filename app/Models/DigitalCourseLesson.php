<?php

namespace App\Models;

use App\Models\Concerns\HasYoutubeUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalCourseLesson extends Model
{
    use HasYoutubeUrl;

    protected $fillable = [
        'digital_course_id',
        'title',
        'youtube_url',
        'sort_order',
        'is_active',
        'is_free',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'is_free' => 'boolean',
        ];
    }

    public function isWatchable(bool $enrolled): bool
    {
        return $this->is_active && ($this->is_free || $enrolled);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(DigitalCourse::class, 'digital_course_id');
    }

    public function getYoutubeVideoIdAttribute(): ?string
    {
        return $this->extractYoutubeVideoId($this->youtube_url);
    }

    public function getThumbnailUrlAttribute(): string
    {
        $videoId = $this->youtube_video_id;

        return $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : '';
    }

    public function getEmbedUrlAttribute(): string
    {
        return $this->embedUrlFor($this->youtube_url);
    }
}
