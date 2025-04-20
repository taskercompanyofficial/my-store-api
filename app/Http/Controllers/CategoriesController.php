<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = Categories::with(['parent', 'children'])->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')
            ]
        ]);

        try {
            $category = Categories::create($validated);
            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create category'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categories $category): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Category retrieved successfully',
            'data' => $category->load(['parent', 'children'])
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categories $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->whereNot('id', $category->id)
            ]
        ]);
        try {
            $category->update($validated);
            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categories $category): JsonResponse
    {
        if ($category->children()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category with child categories'
            ], 422);
        }

        if ($category->products()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category with associated products'
            ], 422);
        }

        $category->delete();
        return response()->json(null, 204);
    }
}
