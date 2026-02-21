<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Restaurant;
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
}