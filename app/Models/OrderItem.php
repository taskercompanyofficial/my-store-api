<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'tax',
        'total',
        'product_snapshot'
    ];

    protected $casts = [
        'product_snapshot' => 'array',
        'unit_price' => 'float',
        'subtotal' => 'float',
        'tax' => 'float',
        'total' => 'float'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        // You can implement custom tax calculation logic here
        $this->tax = $this->subtotal * 0.15; // Example: 15% tax
        $this->total = $this->subtotal + $this->tax;
        $this->save();
    }
} 