<?php

namespace App\Models\Concerns;

trait HasYoutubeUrl
{
    public function extractYoutubeVideoId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $match)) {
            return $match[1];
        }

        return null;
    }

    public function getYoutubeVideoIdFromUrl(?string $url): ?string
    {
        return $this->extractYoutubeVideoId($url);
    }

    public function embedUrlFor(?string $url): string
    {
        $videoId = $this->extractYoutubeVideoId($url);

        return $videoId ? "https://www.youtube.com/embed/{$videoId}" : '';
    }

    public function youtubeThumbnailFor(?string $url): string
    {
        $videoId = $this->extractYoutubeVideoId($url);

        return $videoId ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg" : '';
    }
}
