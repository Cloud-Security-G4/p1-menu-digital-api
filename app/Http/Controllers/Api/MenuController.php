<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MenuController extends Controller
{
    /**
     * GET /api/v1/menu/:slug
     * Obtener menú público de un restaurante por su slug
     */
    public function getPublicMenu(Request $request)
    {
        $restaurant = Restaurant::where('slug', $request->slug)->first();
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $restaurant->load('categories.dishes');

        return response()->json([
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'slug' => $restaurant->slug,
            'description' => $restaurant->description,
            'categories' => $restaurant->categories->sortBy('position')->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'position' => $category->position,
                    'dishes' => $category->dishes
                        ->where('available', true)
                        ->sortBy('position')
                        ->values()
                ];
            })->values()
        ]);
    }
    /**
     * GET /api/v1/admin/qr
     * Obtener URL del código QR para el menú público del restaurante del usuario autenticado
     */
    public function getRestaurantMenuQr(Request $request)
    {
        $restaurant = Restaurant::where('user_id', $request->user()->id)
                    ->first();
        if (! $restaurant) {
            return response()->json([
            'message' => 'El restaurante no existe o no pertenece al usuario'
            ], 404);
        }

        // Parámetros opcionales
        $size = (int) $request->query('size', 250);

        // Validaciones básicas
        $size = $size > 0 && $size <= 1000 ? $size : 250;

        // URL pública del menú
        $menuUrl = env('FRONTEND_URL') . '/m/' . $restaurant->slug;

        // Generar QR
        $qr = QrCode::format('svg')
            ->size($size)
            ->generate($menuUrl);

        return response($qr, 200)
            ->header('Content-Type', 'image/svg+xml');

        if ($format === 'svg') {
            return response($qr, 200)
                ->header('Content-Type', 'image/svg+xml');
        }

        return response($qr, 200)
            ->header('Content-Type', 'image/png');
    }

}
