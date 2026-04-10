<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRequest extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'device_name',
        'specifications',
        'min_price',
        'max_price',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
