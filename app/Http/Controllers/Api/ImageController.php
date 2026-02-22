<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Restaurant;
use App\Jobs\ProcessImageJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{

    /**
     * POST /api/v1/admin/upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB
        ]);

        $restaurant = Restaurant::where('user_id', $request->user()->id)
                    ->first();

        if (! $restaurant) {
            return response()->json([
            'message' => 'El restaurante no existe o no pertenece al usuario'
            ], 404);
        }

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurante no encontrado'
            ], 404);
        }

        $file = $request->file('image');

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            "images/{$restaurant->id}",
            $filename,
            'public'
        );

        $image = Image::create([
            'restaurant_id' => $restaurant->id,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        ProcessImageJob::dispatch($image->id);

        return response()->json([
            $image
        ], 201);
    }

    /**
     * DELETE /api/v1/admin/upload/{filename}
     */
    public function delete(Request $request, $filename)
    {
        $restaurant = Restaurant::where('user_id', $request->user()->id)
                    ->first();

        if (! $restaurant) {
            return response()->json([
            'message' => 'El usuario no tiene restaurante asociado'
            ], 404);
        }

        $image = Image::where('restaurant_id', $restaurant->id)
            ->where('filename', $filename)
            ->first();

        if (!$image) {
            return response()->json([
                'message' => 'Imagen no encontrada'
            ], 404);
        }

        Storage::disk('public')->delete($image->path);

        $image->delete();

        return response()->json([
            'message' => 'Imagen eliminada correctamente'
        ]);
    }

    public function test()
    {
        $this->manager = new ImageManager(new Driver());

        $image = Image::where('thumbnail_path', null)
            ->where('medium_path', null)
            ->where('large_path', null)
            ->first();

        if (!$image) {
            Log::error('Image not found', ['image_id' => $this->imageId]);
            return;
        }
        $paths = $this->process(
            Storage::disk('public')->path($image->path),
            "images/{$image->restaurant_id}/processed"
        );

        $image->update([
            'thumbnail_path' => $paths['thumbnail']['path'],
            'medium_path' => $paths['medium']['path'],
            'large_path' => $paths['large']['path'],
        ]);
    }

    public function process(
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