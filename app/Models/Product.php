<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'device_id',
        'brand',
        'model',
        'color',
        'name',
        'slug',
        'description',
        'defects',
        'condition_notes',
        'accessories',
        'disassembled_is',
        'reason_disassembly',
        'price',
        'condition',
        'status',
        'source', // inventory or user
        'location', // for user items
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    
    // Helper method to check if inventory product is out of stock
    public function isOutOfStock()
    {
        if ($this->source !== 'inventory') {
            return false; // User products don't have stock tracking
        }
        
        return $this->getTotalStock() === 0;
    }
    
    // Get total stock across all variants
    public function getTotalStock()
    {
        if ($this->source !== 'inventory') {
            return null;
        }
        
        return $this->variants()->sum('stock_quantity');
    }

    public function getPrimaryImageUrlAttribute()
    {
        if ($this->images->count() > 0) {
            return asset('storage/' . $this->images->first()->image_path);
        }
        return asset('images/placeholder-phone.png'); // Ensure this exists or use a CDN/Generic URL
    }
}
