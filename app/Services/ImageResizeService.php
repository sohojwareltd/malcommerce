<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageResizeService
{
    /**
     * Convert human-readable memory size to bytes
     */
    private static function convertToBytes(string $size): int
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size) - 1]);
        $size = (int) $size;
        
        switch ($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }
        
        return $size;
    }
    
    /**
     * Convert bytes to human-readable memory size
     */
    private static function convertToHumanReadable(int $bytes): string
    {
        $units = ['B', 'K', 'M', 'G'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . $units[$pow];
    }
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
            $fileSize = $file->getSize();
            
            // For very large images, increase memory limit temporarily
            $originalMemoryLimit = ini_get('memory_limit');
            
            // Calculate required memory (rough estimate: width * height * 4 bytes per pixel)
            // For safety, use file size * 3 as memory requirement estimate
            $estimatedMemoryNeeded = $fileSize * 3;
            
            // If estimated memory needed is more than current limit, increase it
            if ($estimatedMemoryNeeded > self::convertToBytes($originalMemoryLimit)) {
                $newLimit = max(512 * 1024 * 1024, $estimatedMemoryNeeded * 1.5); // 512MB minimum or 1.5x estimated
                ini_set('memory_limit', self::convertToHumanReadable($newLimit));
            }
            
            // Create image resource from file with error suppression
            $sourceImage = false;
            $lastError = null;
            
            // Capture any errors
            set_error_handler(function($errno, $errstr) use (&$lastError) {
                $lastError = $errstr;
                return true;
            });
            
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
                    restore_error_handler();
                    // Restore memory limit
                    if ($estimatedMemoryNeeded > self::convertToBytes($originalMemoryLimit)) {
                        ini_set('memory_limit', $originalMemoryLimit);
                    }
                    return $file->store($path, $disk);
            }
            
            restore_error_handler();
            
            if (!$sourceImage) {
                // Restore memory limit
                if ($estimatedMemoryNeeded > self::convertToBytes($originalMemoryLimit)) {
                    ini_set('memory_limit', $originalMemoryLimit);
                }
                $errorMsg = $lastError ?: 'Unknown error creating image resource';
                \Log::warning('Failed to create image resource from file: ' . $sourcePath . ' | Error: ' . $errorMsg);
                throw new \Exception('Failed to process image: ' . $errorMsg);
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
            
            // Restore memory limit
            if (isset($fileSize) && $fileSize > 5 * 1024 * 1024 && isset($originalMemoryLimit)) {
                ini_set('memory_limit', $originalMemoryLimit);
            }
            
            return $fullPath;
            
        } catch (\Exception $e) {
            // Restore memory limit if it was changed
            if (isset($fileSize) && $fileSize > 5 * 1024 * 1024 && isset($originalMemoryLimit)) {
                ini_set('memory_limit', $originalMemoryLimit);
            }
            
            \Log::error('Image resize failed: ' . $e->getMessage() . ' | File: ' . $file->getClientOriginalName() . ' | Size: ' . $file->getSize() . ' bytes');
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // If resize fails, try to store original
            try {
                return $file->store($path, $disk);
            } catch (\Exception $storeException) {
                \Log::error('Failed to store original file: ' . $storeException->getMessage());
                throw new \Exception('Failed to upload photo. Please try again with a smaller image or different format.');
            }
        }
    }
}

