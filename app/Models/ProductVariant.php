<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color_name',
        'color_code',
        'stock_quantity',
        'price_modifier',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
