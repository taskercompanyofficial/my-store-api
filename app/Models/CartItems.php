<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    protected $fillable = ['user_id', 'session_id', 'product_id', 'quantity', 'unit_price', 'subtotal', 'product_snapshot'];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
