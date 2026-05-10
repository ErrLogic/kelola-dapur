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
        $image = $manager->read($file->getRealPath())
            ->scaleDown(1200, 1200);
            
        // Encode as WebP with 80% quality for optimal compression
        $encodedImage = (string) $image->toWebp(80);

        // Upload the compressed raw string data directly to MinIO
        \Illuminate\Support\Facades\Storage::disk($this->disk)->put($path, $encodedImage);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && !str_starts_with($path, 'http')) {
            try {
                Storage::disk($this->disk)->delete($path);
            } catch (\Throwable $e) {
                // Silently ignore deletion errors (e.g. if file doesn't exist)
            }
        }
    }

    public function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // Generate a 7-day presigned URL for private bucket access (matching Python's presigned_get_object)
        return Storage::disk($this->disk)->temporaryUrl($path, now()->addDays(7));
    }
}

