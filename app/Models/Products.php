<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'price',
        'cost_price',
        'wholesale_price', 
        'description',
        'short_description',
        'category',
        'sub_category',
        'brand',
        'manufacturer',
        'volume',
        'weight',
        'dimensions',
        'min_stock_level',
        'max_stock_level',
        'quantity',
        'discount',
        'tax_rate',
        'shipping_class',
        'skin_type',
        'benefits',
        'ingredients',
        'expiry_date',
        'manufacturing_date',
        'warranty',
        'badge',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
        'images',
        'videos',
    ];

    protected $casts = [
        'price' => 'float',
        'cost_price' => 'float',
        'wholesale_price' => 'float',
        'volume' => 'float',
        'weight' => 'float',
        'min_stock_level' => 'integer',
        'max_stock_level' => 'integer',
        'quantity' => 'integer',
        'discount' => 'float',
        'skin_type' => 'string',
        'benefits' => 'string',
        'ingredients' => 'string',
        'meta_keywords' => 'string',
        'tags' => 'string',
        'images' => 'array',
        'videos' => 'array',
        'expiry_date' => 'date',
        'manufacturing_date' => 'date'
    ];
    
    protected $appends = ['rating', 'reviews'];
    
    public function getRatingAttribute()
    {
        // Placeholder for actual rating calculation
        return rand(3, 5);
    }
    
    public function getReviewsAttribute()
    {
        // Placeholder for actual review count
        return rand(0, 100);
    }
}
