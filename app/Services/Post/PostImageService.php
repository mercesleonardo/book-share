<?php

namespace App\Services\Post;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostImageService
{
    public function storeImage(UploadedFile $file, string $disk = 'public', string $dir = 'posts'): string
    {
        return $file->store($dir, $disk);
    }

    public function deleteImage(?string $path, string $disk = 'public'): void
    {
        if ($path) {
            Storage::disk($disk)->delete($path);
        }
    }
}
