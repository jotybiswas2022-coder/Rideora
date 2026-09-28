<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class FileUploader
{
    /**
     * Store an uploaded file with a generated, safe filename on the public disk.
     */
    public static function store(UploadedFile $file, string $directory): string
    {
        $extension = strtolower($file->extension() ?: 'jpg');
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'jpg';

        $name = now()->format('Ymd').'-'.Str::lower(Str::random(16)).'.'.$extension;

        return $file->storeAs(trim($directory, '/'), $name, 'public');
    }

    /**
     * Remove a previously stored file (ignores remote URLs).
     */
    public static function delete(?string $path): void
    {
        if (blank($path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
