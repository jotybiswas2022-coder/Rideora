<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    /**
     * Serve a file from the public disk.
     *
     * Vehicle images, payment proofs and avatars are streamed through the
     * application so they work identically under `php artisan serve`, Apache
     * and nginx (the built in PHP server refuses symlinked directories).
     */
    public function show(Request $request, string $path): BinaryFileResponse
    {
        $path = ltrim($path, '/');

        // Never allow directory traversal outside the public disk.
        abort_if(str_contains($path, '..'), 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        return response()->file($disk->path($path), [
            'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
