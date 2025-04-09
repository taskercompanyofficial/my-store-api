<?php

namespace App\Http\Controllers;

use App\Models\CartItems;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $cartItems = CartItems::where('user_id', $userId)
            ->with('product')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems,
            'total_items' => $cartItems->sum('quantity')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();
        $productId = $request->product_id;
        $quantity = $request->quantity;

        // Get product details
        $product = Products::findOrFail($productId);

        // Check if item already exists in cart
        $cartItem = CartItems::where('product_id', $productId)
            ->where('user_id', $userId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = CartItems::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CartItems $cartItems)
    {
        return response()->json([
            'status' => 'success',
            'data' => $cartItems->with('product')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartItems $cartItem)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify ownership
        $userId = Auth::id();

        if ($cartItem->user_id != $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to cart item'
            ], 403);
        }

        // Update quantity
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully',
            'data' => $cartItem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItems $cartItem)
    {
        // Verify ownership
        $userId = Auth::id();

        if ($cartItem->user_id != $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to cart item'
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item removed successfully'
        ]);
    }

    /**
     * Clear all items from the cart.
     */
    public function clearCart()
    {
        $userId = Auth::id();

        CartItems::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Check if a product is in the user's cart.
     */
    public function checkInCart(Request $request, $product_id)
    {
        try {
            $userId = $request->user()->id;

            $productId = $product_id;

            $cartItem = CartItems::where('product_id', $productId)
                ->where('user_id', $userId)
                ->first();

            return response()->json([
                'status' => 'success',
                'in_cart' => $cartItem ? true : false,
                'quantity' => $cartItem ? $cartItem->quantity : 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking cart: ' . $e->getMessage()
            ], 500);
        }
    }
}
