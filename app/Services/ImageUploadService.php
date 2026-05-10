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

        $extension = $file->getClientOriginalExtension() ?: $file->extension();
        $filename = Str::ulid() . '.' . $extension;
        
        $directory = $slug ? $this->directory . '/' . $slug : $this->directory;
        // Upload without public ACL since the bucket is explicitly private
        $path = $file->storeAs($directory, $filename, $this->disk);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
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

