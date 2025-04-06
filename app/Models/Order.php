<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'shipping_method',
        'shipping_address',
        'billing_address',
        'notes',
        'paid_at'
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'paid_at' => 'datetime',
        'subtotal' => 'float',
        'tax' => 'float',
        'shipping' => 'float',
        'total' => 'float'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = now()->format('YmdHis');
        $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }

    public function updateTotals(): void
    {
        $items = $this->items;
        
        $this->subtotal = $items->sum('subtotal');
        $this->tax = $items->sum('tax');
        // You can implement custom shipping calculation logic here
        $this->total = $this->subtotal + $this->tax + $this->shipping;
        
        $this->save();
    }
} 