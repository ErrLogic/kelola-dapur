<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    protected string $disk = 's3';
    protected string $directory = 'recipes';

    public function upload(UploadedFile $file, ?string $slug = null, ?string $existingPath = null): string
    {
        if ($existingPath) {
            $this->delete($existingPath);
        }

        // Always save as highly optimized WebP format
        $filename = Str::ulid() . '.webp';
        
        $directory = $slug ? $this->directory . '/' . $slug : $this->directory;
        $path = $directory . '/' . $filename;

        // Initialize ImageManager with the built-in GD driver
        $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        
        // Read the file, scale it down to a max width/height of 1200px (keeps aspect ratio)
        $image = $manager->decode($file->getRealPath())
            ->scaleDown(1200, 1200);
            
        // Encode as WebP with 80% quality for optimal compression
        $encodedImage = (string) $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 80));

        // Upload the compressed raw string data directly to MinIO
        \Illuminate\Support\Facades\Storage::disk($this->disk)->put($path, $encodedImage);

        // Return the 7-day presigned URL to be saved in the database (matches Python project behavior)
        return \Illuminate\Support\Facades\Storage::disk($this->disk)->temporaryUrl($path, now()->addDays(7));
    }

    public function delete(?string $pathOrUrl): void
    {
        if (!$pathOrUrl) return;
        
        $path = $this->extractPath($pathOrUrl);

        if ($path && !str_starts_with($path, 'http')) {
            try {
                Storage::disk($this->disk)->delete($path);
            } catch (\Throwable $e) {
                // Silently ignore deletion errors (e.g. if file doesn't exist)
            }
        }
    }

    public function url(?string $pathOrUrl): ?string
    {
        if (!$pathOrUrl) {
            return null;
        }

        $path = $this->extractPath($pathOrUrl);

        if (str_starts_with($path, 'http')) {
            return $path; // It's an external URL (not on our disk)
        }

        // Generate a 7-day presigned URL for private bucket access
        return Storage::disk($this->disk)->temporaryUrl($path, now()->addDays(7));
    }

    protected function extractPath(string $url): string
    {
        if (!str_starts_with($url, 'http')) {
            return ltrim($url, '/');
        }

        $baseUrl = Storage::disk($this->disk)->url('');
        
        if (str_starts_with($url, $baseUrl)) {
            return ltrim(substr($url, strlen($baseUrl)), '/');
        }

        // Fallback for paths with query strings or minor URL differences
        $urlPath = parse_url($url, PHP_URL_PATH) ?? '';
        $baseUrlPath = parse_url($baseUrl, PHP_URL_PATH) ?? '';
        
        if ($baseUrlPath && str_starts_with($urlPath, $baseUrlPath)) {
            return ltrim(substr($urlPath, strlen($baseUrlPath)), '/');
        }

        return $url;
    }
}

