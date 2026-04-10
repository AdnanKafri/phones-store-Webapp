<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_variant_id',
        'order_type',
        'status',
        'total_price',
        'shipping_address',
        'seller_approval',
        'admin_approval',
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // Buyer
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
