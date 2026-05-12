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
        Storage::disk($this->disk)->put($path, $encodedImage);

        // Store only the object key in the DB. URLs are signed fresh on every render
        // via url() so they never expire and never get double-signed.
        return $path;
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
            return $path; // External URL we don't manage
        }

        // Generate a fresh 7-day presigned URL every time
        return Storage::disk($this->disk)->temporaryUrl($path, now()->addDays(7));
    }

    protected function extractPath(string $url): string
    {
        if (!str_starts_with($url, 'http')) {
            // Already an object key. Strip any leading slash + any accidental query string.
            return ltrim(strtok($url, '?'), '/');
        }

        // For full URLs (e.g. old DB rows with a presigned URL), always work with
        // just the path component — never the query string — to avoid double-signing.
        $urlPath = parse_url($url, PHP_URL_PATH) ?? '';
        $baseUrlPath = parse_url(Storage::disk($this->disk)->url(''), PHP_URL_PATH) ?? '';

        if ($baseUrlPath && $baseUrlPath !== '/' && str_starts_with($urlPath, $baseUrlPath)) {
            return ltrim(substr($urlPath, strlen($baseUrlPath)), '/');
        }

        return ltrim($urlPath, '/');
    }
}

