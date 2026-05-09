<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    protected string $disk = 's3';
    protected string $directory = 'recipes';

    public function upload(UploadedFile $file, ?string $existingPath = null): string
    {
        if ($existingPath) {
            $this->delete($existingPath);
        }

        $filename = Str::ulid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($this->directory, $filename, $this->disk);

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

        return Storage::disk($this->disk)->url($path);
    }
}

