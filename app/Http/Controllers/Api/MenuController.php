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
        $sizeParam = strtoupper($request->query('size', 'M'));
        $format = strtolower($request->query('format', 'svg'));

        // Mapeo de tamaños
        $sizes = [
            'S'  => 200,
            'M'  => 400,
            'L'  => 800,
            'XL' => 1200,
        ];

        $size = $sizes[$sizeParam] ?? 400;

        // Validar formato
        if (!in_array($format, ['svg', 'png'])) {
            $format = 'svg';
        }

        // URL pública del menú
        $menuUrl = rtrim(env('FRONTEND_URL'), '/') . '/m/' . $restaurant->slug;

        // Generar QR con nivel de corrección alto (H = 30%)
        $qrBuilder = QrCode::format($format)
            ->size($size)
            ->errorCorrection('H');

        $qr = $qrBuilder->generate($menuUrl);

        $contentType = $format === 'png'
            ? 'image/png'
            : 'image/svg+xml';

        return response($qr, 200)
            ->header('Content-Type', $contentType);
    }

}
