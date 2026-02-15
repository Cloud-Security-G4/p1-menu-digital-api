<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DishController extends Controller
{
    /**
     * GET /api/v1/admin/restaurant
     * Obtener restaurante del usuario autenticado
     */
    public function index(Request $request)
    {
        $restaurant = Restaurant::where('user_id', auth()->id())->get();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El usuario no tiene un restaurante creado'
            ], 404);
        }

        return response()->json($restaurant);
    }

    /**
     * GET /api/v1/admin/restaurant/{id}
     * Obtener restaurante del usuario autenticado
     */
    public function show(Request $request, $id)
    {
        $restaurant = Restaurant::where('user_id', auth()->id())->where('id', $id)->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El restaurante no existe o no pertenece al usuario'
            ], 404);
        }

        return response()->json($restaurant);
    }

    /**
     * POST /api/v1/admin/restaurant
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
            'name'      => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'phone'    => 'nullable|string',
            'address'   => 'nullable|string',
            'hours'    => 'nullable|array',
            'logo'        => 'nullable|url',
        ]);
        

        $restaurant = Restaurant::create([
            'user_id'     => $request->user()->id,
            'name'      => $data['name'],
            'description' => $data['description'] ?? null,
            'phone'    => $data['phone'] ?? null,
            'address'   => $data['address'] ?? null,
            'hours'    => $data['hours'] ?? null,
            'logo'        => $data['logo'] ?? null,
        ]);

        return response()->json($restaurant, 201);
    }

    /**
     * PUT /api/v1/admin/restaurant
     * Actualizar restaurante
     */
    public function update(Request $request)
    {
        $restaurant = Restaurant::where('user_id', $request->user()->id)->firstOrFail();

        $data = $request->validate([
            'name'      => 'sometimes|required|string|max:100',
            'description' => 'nullable|string|max:500',
            'phone'    => 'nullable|string',
            'address'   => 'nullable|string',
            'hours'    => 'nullable|array',
            'logo'        => 'nullable|url',
        ]);

        $restaurant->update($data);

        return response()->json($restaurant);
    }

    /**
     * DELETE /api/v1/admin/restaurant
     * Eliminar restaurante
     */
    public function destroy(Request $request)
    {
        $restaurant = Restaurant::where('user_id', $request->user()->id)
                    ->where('id', $request->id)
                    ->first();

        if (! $restaurant) {
            return response()->json([
            'message' => 'El restaurante no existe o no pertenece al usuario'
            ], 404);
        }

        $restaurant->delete();

        return response()->json([
            'message' => 'Restaurante eliminado correctamente'
        ]);
    }
}
