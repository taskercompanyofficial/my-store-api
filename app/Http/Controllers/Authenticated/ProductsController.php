<?php

namespace App\Http\Controllers\Authenticated;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::all();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'price' => 'required|numeric',
            'cost_price' => 'nullable|numeric',
            'wholesale_price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'category' => 'required|string',
            'sub_category' => 'nullable|string',
            'brand' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'volume' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'dimensions' => 'nullable|string',
            'min_stock_level' => 'nullable|integer',
            'max_stock_level' => 'nullable|integer',
            'quantity' => 'required|integer',
            'discount' => 'required|numeric',
            'tax_rate' => 'nullable|string',
            'shipping_class' => 'nullable|string',
            'skin_type' => 'nullable|string',
            'benefits' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'manufacturing_date' => 'nullable|date',
            'warranty' => 'nullable|string',
            'badge' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'tags' => 'nullable|string',
            'images' => 'nullable|array',
            'videos' => 'nullable|array',
        ]);
        $user = $request->user();
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        $validated['user_id'] = $user->id;

        try {
            $product = Products::create($validated);
            return response()->json(['status' => 'success', 'message' => 'Product created successfully', 'product' => $product]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create product' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $product = Products::where('slug', $slug)->first();
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Products::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sku' => 'nullable|string',
            'barcode' => 'nullable|string',
            'price' => 'sometimes|numeric',
            'cost_price' => 'nullable|numeric',
            'wholesale_price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'category' => 'sometimes|string',
            'sub_category' => 'nullable|string',
            'brand' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'volume' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'dimensions' => 'nullable|string',
            'min_stock_level' => 'nullable|integer',
            'max_stock_level' => 'nullable|integer',
            'quantity' => 'sometimes|integer',
            'discount' => 'sometimes|numeric',
            'tax_rate' => 'nullable|string',
            'shipping_class' => 'nullable|string',
            'skin_type' => 'nullable|string',
            'benefits' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'manufacturing_date' => 'nullable|date',
            'warranty' => 'nullable|string',
            'badge' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'tags' => 'nullable|string',
            'images' => 'nullable|array',
            'videos' => 'nullable|array',
            'documents' => 'nullable|array',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        try {
            $product->update($validated);
            return response()->json(['status' => 'success', 'message' => 'Product updated successfully', 'product' => $product]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update product' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Products::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
