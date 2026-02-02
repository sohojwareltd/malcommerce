<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'youtube_link',
        'thumbnail',
        'title',
        'category',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get YouTube video ID from link
     */
    public function getYoutubeVideoIdAttribute(): ?string
    {
        $url = $this->youtube_link;
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * Get thumbnail URL - custom or YouTube default
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return str_starts_with($this->thumbnail, 'http') ? $this->thumbnail : asset('storage/' . $this->thumbnail);
        }
        $videoId = $this->youtube_video_id;
        return $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : '';
    }

    /**
     * Get YouTube embed URL
     */
    public function getEmbedUrlAttribute(): string
    {
        $videoId = $this->youtube_video_id;
        return $videoId ? "https://www.youtube.com/embed/{$videoId}" : '';
    }

    /**
     * Get YouTube watch URL
     */
    public function getWatchUrlAttribute(): string
    {
        $videoId = $this->youtube_video_id;
        return $videoId ? "https://www.youtube.com/watch?v={$videoId}" : '';
    }
}
