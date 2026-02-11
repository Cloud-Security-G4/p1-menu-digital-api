<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    /**
     * GET /api/v1/admin/restaurant
     * Obtener restaurante del usuario autenticado
     */
    public function index(Request $request)
    {

        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El usuario no tiene un restaurante creado'
            ], 404);
        }

        return response()->json($restaurant);
    }

    /**
     * POST /api/v1/admin/restaurante
     * Crear restaurante
     */
    public function store(Request $request)
    {
        // Opcional: bloquear si ya existe uno
        if ($request->user()->restaurante) {
            return response()->json([
                'message' => 'El usuario ya tiene un restaurante'
            ], 409);
        }

        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'telefono'    => 'nullable|string',
            'direccion'   => 'nullable|string',
            'horarios'    => 'nullable|array',
            'logo'        => 'nullable|url',
        ]);

        $restaurant = Restaurant::create([
            'id'          => Str::uuid(),
            'user_id'     => $request->user()->id,
            'nombre'      => $data['nombre'],
            'slug'        => Str::slug($data['nombre']),
            'descripcion' => $data['descripcion'] ?? null,
            'telefono'    => $data['telefono'] ?? null,
            'direccion'   => $data['direccion'] ?? null,
            'horarios'    => $data['horarios'] ?? null,
            'logo'        => $data['logo'] ?? null,
        ]);

        return response()->json($restaurant, 201);
    }

    /**
     * PUT /api/v1/admin/restaurante
     * Actualizar restaurante
     */
    public function update(Request $request)
    {
        $restaurant = Restaurant::where('user_id', $request->user()->id)->firstOrFail();

        $data = $request->validate([
            'nombre'      => 'sometimes|required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'telefono'    => 'nullable|string',
            'direccion'   => 'nullable|string',
            'horarios'    => 'nullable|array',
            'logo'        => 'nullable|url',
        ]);

        if (isset($data['nombre'])) {
            $data['slug'] = Str::slug($data['nombre']);
        }

        $restaurant->update($data);

        return response()->json($restaurant);
    }

    /**
     * DELETE /api/v1/admin/restaurante
     * Eliminar restaurante
     */
    public function destroy(Request $request)
    {
        $restaurant = Restaurant::where('user_id', $request->user()->id)->firstOrFail();
        $restaurant->delete();

        return response()->json([
            'message' => 'Restaurante eliminado correctamente'
        ]);
    }
}
