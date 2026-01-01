<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageResizeService
{
    /**
     * Resize and optimize an uploaded image using native GD library
     * 
     * @param UploadedFile $file The uploaded file
     * @param string $path Storage path (e.g., 'photos')
     * @param int $width Target width in pixels (default: 400)
     * @param int $height Target height in pixels (default: 400)
     * @param int $quality JPEG quality (1-100, default: 85)
     * @param string $disk Storage disk (default: 'public')
     * @return string The stored file path
     */
    public static function resizeAndStore(
        UploadedFile $file,
        string $path = 'photos',
        int $width = 400,
        int $height = 400,
        int $quality = 85,
        string $disk = 'public'
    ): string {
        // Check if GD extension is available
        if (!extension_loaded('gd')) {
            // Fallback: store original image if GD is not available
            \Log::warning('GD extension not available, storing original image');
            return $file->store($path, $disk);
        }
        
        try {
            $sourcePath = $file->getRealPath();
            $mimeType = $file->getMimeType();
            
            // Create image resource from file
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $sourceImage = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    return $file->store($path, $disk);
            }
            
            if (!$sourceImage) {
                return $file->store($path, $disk);
            }
            
            // Get original dimensions
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);
            
            // Calculate aspect ratios
            $originalRatio = $originalWidth / $originalHeight;
            $targetRatio = $width / $height;
            
            // Create new image with target dimensions
            $resizedImage = imagecreatetruecolor($width, $height);
            
            // Fill with white background (for better quality when converting to JPEG)
            $white = imagecolorallocate($resizedImage, 255, 255, 255);
            imagefill($resizedImage, 0, 0, $white);
            
            // Calculate scaling and crop position for center crop
            if ($originalRatio > $targetRatio) {
                // Original is wider - scale to fit height, crop width from center
                // We need to crop the width, so calculate how much width we need from source
                $cropHeight = $originalHeight;
                $cropWidth = (int)($originalHeight * $targetRatio); // Width that matches target ratio
                $cropX = (int)(($originalWidth - $cropWidth) / 2); // Center crop
                $cropY = 0;
            } else {
                // Original is taller - scale to fit width, crop height from center
                // We need to crop the height, so calculate how much height we need from source
                $cropWidth = $originalWidth;
                $cropHeight = (int)($originalWidth / $targetRatio); // Height that matches target ratio
                $cropX = 0;
                $cropY = (int)(($originalHeight - $cropHeight) / 2); // Center crop
            }
            
            // Resize and crop (center crop)
            imagecopyresampled(
                $resizedImage,
                $sourceImage,
                0, 0, // Destination x, y
                $cropX, $cropY, // Source x, y (crop position)
                $width, $height, // Destination width, height
                $cropWidth, $cropHeight // Source width, height (cropped area)
            );
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.jpg';
            $fullPath = $path . '/' . $filename;
            
            // Save as JPEG with quality
            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            imagejpeg($resizedImage, $tempFile, $quality);
            
            // Store the resized image
            Storage::disk($disk)->put($fullPath, file_get_contents($tempFile));
            
            // Clean up
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);
            @unlink($tempFile);
            
            return $fullPath;
            
        } catch (\Exception $e) {
            \Log::warning('Image resize failed: ' . $e->getMessage());
            return $file->store($path, $disk);
        }
    }
}

