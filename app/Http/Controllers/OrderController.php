<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = Order::with('items')->where('user_id', $user->id)->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
            'shipping_address' => 'required|array',
            'billing_address' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => Order::generateOrderNumber(),
                'shipping_method' => $validated['shipping_method'],
                'payment_method' => $validated['payment_method'],
                'shipping_address' => $validated['shipping_address'],
                'billing_address' => $validated['billing_address'],
                'notes' => $validated['notes'] ?? null,
                'shipping' => 0, // You can implement shipping cost calculation
                'order_status' => 'pending',
                'payment_status' => 'pending'
            ]);

            foreach ($validated['items'] as $item) {
                $product = Products::findOrFail($item['product_id']);
                
                // Check stock availability
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Create order item
                $orderItem = new OrderItem([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'product_snapshot' => $product->toArray()
                ]);

                $order->items()->save($orderItem);
                $orderItem->calculateTotals();

                // Update product quantity
                $product->quantity -= $item['quantity'];
                $product->save();
            }

            $order->updateTotals();
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'order' => $order->load('items')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show(string $orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        return response()->json($order);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|string|in:pending,paid,failed,refunded'
        ]);

        $order->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }
} 