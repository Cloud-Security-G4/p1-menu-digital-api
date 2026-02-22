<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use App\Models\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class ProcessImageJob implements ShouldQueue
{
    use Queueable;

    private ImageManager $manager;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $imageId)
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Job started', [
            'image' => $this->imageId
        ]);

        $image = Image::find($this->imageId);
        
        if (!$image) {
            Log::error('Image not found', ['image_id' => $this->imageId]);
            return;
        }
        $paths = $this->createWebpVersions(
            Storage::disk('public')->path($image->path),
            "images/{$image->restaurant_id}/processed"
        );

        $image->update([
            'thumbnail_path' => $paths['thumbnail']['path'],
            'medium_path' => $paths['medium']['path'],
            'large_path' => $paths['large']['path'],
        ]);
    }

    private function createWebpVersions(
        string $imagePath,
        string $outputDirectory = 'images/processed',
        string $disk = 'public'
    ): array {
        if (! file_exists($imagePath)) {
            throw new \RuntimeException("Source image not found: {$imagePath}");
        }

        $baseFilename = pathinfo($imagePath, PATHINFO_FILENAME);
        $paths        = [];
        $sizes = [
            'thumbnail' => ['width' => 150,  'height' => 150],
            'medium'    => ['width' => 600,  'height' => 600],
            'large'     => ['width' => 1200, 'height' => 1200],
        ];
        foreach ($sizes as $size => $dimensions) {
            $image = $this->manager->read($imagePath);

            // Scale down proportionally, never upscale, crop to fill the exact box
            $image->cover($dimensions['width'], $dimensions['height']);

            $filename       = "{$baseFilename}_{$size}.webp";
            $relativePath   = "{$outputDirectory}/{$filename}";

            Storage::disk($disk)->put(
                $relativePath,
                $image->toWebp(quality: 85)->toString()
            );
            $sizes[$size]['path'] = $relativePath;
        }

        return $sizes;
    }
}
