<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DishController extends Controller
{
    /**
     * GET /api/v1/admin/dishes
     * Get all dishes of a specific category for the authenticated user's restaurant
     */
    public function index(Request $request)
    {

        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El usuario no tiene un restaurante creado'
            ], 404);
        }
        $category_id = $request->input('category_id');

        $dish = Dish::whereHas('category', function ($query) use ($restaurant, $category_id) {
            $query->where('restaurant_id', $restaurant->id);
            if ($category_id) {
                $query->where('category_id', $category_id);
            }
        })->get();

        return response()->json($dish);
    }

    /**
     * GET /api/v1/admin/dishes/:id
     * Get a specific dish for the authenticated user's restaurant
     */
    public function show(Request $request, $id)
    {

        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El usuario no tiene un restaurante creado'
            ], 404);
        }

        $dish = Dish::whereHas('category', function ($query) use ($restaurant, $id) {
            $query->where('restaurant_id', $restaurant->id);
        })->where('id', $id)->first();

        if (!$dish) {
            return response()->json([
                'message' => 'El plato no existe o no pertenece al restaurante del usuario'
            ], 404);
        }

        return response()->json($dish);
    }

    /**
     * POST /api/v1/admin/categories/dishes
     * Create dish in a specific category for the authenticated user's restaurant
     */
    public function store(Request $request)
    {
        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El usuario no tiene un restaurante creado'
            ], 404);
        }

        $category = Category::where('id', $request->category_id)
            ->where('restaurant_id', $restaurant->id)
            ->first();

        if (!$category) {
            return response()->json([
                'message' => 'La categoría no existe o no pertenece al restaurante del usuario'
            ], 404);
        }

        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'description'   => 'nullable|string|max:500',
            'price'         => 'required|numeric',
            'offer_price'   => 'nullable|numeric',
            'image_url'     => 'nullable|url',
            'available'     => 'nullable|boolean',
            'featured'      => 'nullable|boolean',
            'tags'          => 'nullable|array',
            'position'      => 'nullable|integer',
            'category_id'   => 'required|exists:categories,id',
        ]);
        $dish = Dish::create([
            'restaurant_id' => $restaurant->id,
            'category_id'   => $data['category_id'],
            'name'          => $data['name'],
            'description'   => $data['description'] ?? null,
            'price'         => $data['price'],
            'offer_price'   => $data['offer_price'] ?? null,
            'image_url'     => $data['image_url'] ?? null,
            'available'     => $data['available'] ?? true,
            'featured'      => $data['featured'] ?? false,
            'tags'          => json_encode($data['tags'] ?? []),
            'position'      => $data['position'] ?? null,
        ]);
        return response()->json($dish, 201);
    }

    /**
     * PUT api/v1/admin/categories/dishes/{id}
     * Update dish in a specific category for the authenticated user's restaurant
     */
    public function update(Request $request, $id)
    {
        $dish = Dish::where('id', $id)
            ->first();
        if (!$dish) {
            return response()->json([
                'message' => 'El plato no existe o no pertenece al restaurante del usuario'
            ], 404);
        }

        $data = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|string|max:100',
            'description'   => 'nullable|string|max:500',
            'price'         => 'required|numeric',
            'offer_price'   => 'nullable|numeric',
            'image_url'     => 'nullable|url',
            'available'     => 'nullable|boolean',
            'featured'      => 'nullable|boolean',
            'tags'          => 'nullable|array',
            'position'      => 'nullable|integer',
        ]);

        $dish->update($data);

        return response()->json($dish);
    }

    /**
     * DELETE /api/v1/admin/dishes/:id
     * delete dish
     */
    public function destroy(Request $request, $id)
    {
        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El usuario no tiene un restaurante creado'
            ], 404);
        }
        $dish = Dish::whereHas('category', function ($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->where('id', $id)->first();

        if (!$dish) {
            return response()->json([
                'message' => 'El plato no existe o no pertenece al restaurante del usuario'
            ], 404);
        }

        $dish->delete();

        return response()->json([
            'message' => 'Plato eliminado correctamente'
        ]);
    }

    /**
     * PATCH /api/v1/admin/dishes/:id
     * toggle dish availability
     */
    public function toggleAvailability(Request $request, $id)
    {
        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El usuario no tiene un restaurante creado'
            ], 404);
        }
        $dish = Dish::whereHas('category', function ($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->where('id', $id)->first();

        if (!$dish) {
            return response()->json([
                'message' => 'El plato no existe o no pertenece al restaurante del usuario'
            ], 404);
        }

        $dish->available = !$dish->available;
        $dish->save();

        return response()->json($dish);
    }
}
