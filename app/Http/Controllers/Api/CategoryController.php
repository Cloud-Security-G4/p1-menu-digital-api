<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * GET /api/v1/admin/categories
     * Get all categories of the authenticated user's restaurant
     */
    public function index(Request $request)
    {
        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El usuario no tiene un restaurante creado'
            ], 404);
        }

        $categories = $restaurant->categories;
        return response()->json($categories);
    }

    /**
     * POST /api/v1/admin/categories
     * Create category
     */
    public function store(Request $request)
    {
        $restaurant = Restaurant::where('user_id', auth()->id())->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'El usuario no tiene un restaurante creado'
            ], 404);
        }

        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'position'    => 'nullable|integer',
            'active'   => 'nullable|boolean',
        ]);
        $category = Category::create([
            'restaurant_id' => $restaurant->id,
            'name'          => $data['name'],
            'description'   => $data['description'] ?? null,
            'position'      => $data['position'] ?? null,
            'active'        => $data['active'] ?? true,
        ]);
        return response()->json($category, 201);
    }

    /**
     * PUT /api/v1/admin/categories/{id}
     * Update category
     */
    public function update(Request $request, $id)
    {
        $category = Category::where('restaurant_id', auth()->user()->restaurant->first()->id)->findOrFail($id);

        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'position'    => 'nullable|integer',
            'active'   => 'nullable|boolean',
        ]);

        $category->update($data);

        return response()->json($category);
    }

    /**
     * DELETE /api/v1/admin/categories/:id
     * delete category
     */
    public function destroy(Request $request, $id)
    {
        $category = Category::where('restaurant_id', auth()->user()->restaurant->first()->id)
            ->where('id', $id)
            ->first();

        if (! $category) {
            return response()->json([
            'message' => 'Categoría no encontrada'
            ], 404);
        }

        if ($category->dishes()->count() > 0) {
            return response()->json([
            'message' => 'No se puede eliminar una categoría que tiene platos asociados'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente'
        ]);
    }

    /**
     * PATCH /api/v1/admin/categories/reorder
     * reorder categories
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.position' => 'required|integer',
        ]);

        foreach ($data['categories'] as $catData) {
            Category::where('id', $catData['id'])
                ->where('restaurant_id', auth()->user()->restaurant->first()->id)
                ->update(['position' => $catData['position']]);
        }

        return response()->json([
            'message' => 'Categorías reordenadas correctamente'
        ]);
    }
}
