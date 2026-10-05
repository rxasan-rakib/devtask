<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    /**
     * Max dimension (width or height) for uploaded task images.
     */
    protected int $maxDimension = 1920;

    /**
     * Compression quality (0-100) for JPEG and WebP.
     * 85 provides near-lossless visual quality while drastically reducing file size.
     */
    protected int $compressionQuality = 85;

    /**
     * Optimize and store an uploaded image or attachment.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return array
     */
    public function optimizeAndStore(UploadedFile $file, string $directory = 'task-attachments'): array
    {
        @ini_set('memory_limit', '256M');

        $originalName = $file->getClientOriginalName();
        $mimeType     = $file->getMimeType() ?: $file->getClientMimeType();
        $extension    = strtolower($file->getClientOriginalExtension());
        $isImage      = str_starts_with($mimeType, 'image/') || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);

        // If not an image, store directly
        if (!$isImage || !extension_loaded('gd')) {
            $path = $file->store($directory, 'public');
            return [
                'file_name' => $originalName,
                'file_path' => $path,
                'file_type' => $mimeType,
                'file_size' => $file->getSize(),
            ];
        }

        try {
            $sourcePath = $file->getRealPath();
            $imageResource = $this->createImageFromFile($sourcePath, $extension, $mimeType);

            if (!$imageResource) {
                // Fallback to standard storage
                $path = $file->store($directory, 'public');
                return [
                    'file_name' => $originalName,
                    'file_path' => $path,
                    'file_type' => $mimeType,
                    'file_size' => $file->getSize(),
                ];
            }

            // Fix EXIF orientation if JPEG
            $imageResource = $this->correctExifOrientation($imageResource, $sourcePath, $extension);

            $origWidth  = imagesx($imageResource);
            $origHeight = imagesy($imageResource);

            // Calculate scaled dimensions if larger than maxDimension
            $newWidth  = $origWidth;
            $newHeight = $origHeight;

            if ($origWidth > $this->maxDimension || $origHeight > $this->maxDimension) {
                if ($origWidth >= $origHeight) {
                    $newWidth  = $this->maxDimension;
                    $newHeight = (int) round(($origHeight / $origWidth) * $this->maxDimension);
                } else {
                    $newHeight = $this->maxDimension;
                    $newWidth  = (int) round(($origWidth / $origHeight) * $this->maxDimension);
                }
            }

            // Create canvas
            $targetCanvas = imagecreatetruecolor($newWidth, $newHeight);

            // Handle transparency for PNG / WebP / GIF
            $hasAlpha = in_array($extension, ['png', 'webp', 'gif']) || str_contains($mimeType, 'png') || str_contains($mimeType, 'webp');
            if ($hasAlpha) {
                imagealphablending($targetCanvas, false);
                imagesavealpha($targetCanvas, true);
                $transparent = imagecolorallocatealpha($targetCanvas, 255, 255, 255, 127);
                imagefilledrectangle($targetCanvas, 0, 0, $newWidth, $newHeight, $transparent);
            } else {
                // White background for JPEG
                $white = imagecolorallocate($targetCanvas, 255, 255, 255);
                imagefilledrectangle($targetCanvas, 0, 0, $newWidth, $newHeight, $white);
            }

            // High quality resampling with antialiasing
            imagecopyresampled($targetCanvas, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            // Destination directory on disk
            $storageDisk = Storage::disk('public');
            $dirPath = storage_path('app/public/' . trim($directory, '/'));
            if (!file_exists($dirPath)) {
                @mkdir($dirPath, 0755, true);
            }

            $uniqueHash = Str::random(24);
            $saveExtension = in_array($extension, ['png', 'gif']) ? $extension : ($extension === 'webp' ? 'webp' : 'jpg');
            $filename = time() . '_' . $uniqueHash . '.' . $saveExtension;
            $targetFilePath = $dirPath . '/' . $filename;
            $relativeStoragePath = trim($directory, '/') . '/' . $filename;

            // Output optimized image based on format
            if ($saveExtension === 'png') {
                imagepng($targetCanvas, $targetFilePath, 6);
            } elseif ($saveExtension === 'webp' && function_exists('imagewebp')) {
                imagewebp($targetCanvas, $targetFilePath, $this->compressionQuality);
            } else {
                imagejpeg($targetCanvas, $targetFilePath, $this->compressionQuality);
            }

            // Clean up memory
            imagedestroy($imageResource);
            imagedestroy($targetCanvas);

            $finalSize = file_exists($targetFilePath) ? filesize($targetFilePath) : $file->getSize();

            return [
                'file_name' => $originalName,
                'file_path' => $relativeStoragePath,
                'file_type' => $saveExtension === 'png' ? 'image/png' : ($saveExtension === 'webp' ? 'image/webp' : 'image/jpeg'),
                'file_size' => $finalSize,
            ];

        } catch (\Throwable $e) {
            // Safe fallback if GD error occurs
            $path = $file->store($directory, 'public');
            return [
                'file_name' => $originalName,
                'file_path' => $path,
                'file_type' => $mimeType,
                'file_size' => $file->getSize(),
            ];
        }
    }

    /**
     * Create GD image resource from file path.
     */
    protected function createImageFromFile(string $path, string $extension, string $mimeType)
    {
        if (!file_exists($path)) {
            return null;
        }

        return match(true) {
            str_contains($mimeType, 'jpeg') || in_array($extension, ['jpg', 'jpeg']) => @imagecreatefromjpeg($path),
            str_contains($mimeType, 'png')  || $extension === 'png'                  => @imagecreatefrompng($path),
            str_contains($mimeType, 'webp') || $extension === 'webp'                 => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            str_contains($mimeType, 'gif')  || $extension === 'gif'                  => @imagecreatefromgif($path),
            default => @imagecreatefromstring(file_get_contents($path)),
        };
    }

    /**
     * Correct EXIF orientation for smartphone photos.
     */
    protected function correctExifOrientation($resource, string $filePath, string $extension)
    {
        if (!function_exists('exif_read_data') || !in_array($extension, ['jpg', 'jpeg'])) {
            return $resource;
        }

        try {
            $exif = @exif_read_data($filePath);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $rotated = imagerotate($resource, 180, 0);
                        imagedestroy($resource);
                        return $rotated;
                    case 6:
                        $rotated = imagerotate($resource, -90, 0);
                        imagedestroy($resource);
                        return $rotated;
                    case 8:
                        $rotated = imagerotate($resource, 90, 0);
                        imagedestroy($resource);
                        return $rotated;
                }
            }
        } catch (\Throwable) {
            // Ignore exif errors
        }

        return $resource;
    }
}
