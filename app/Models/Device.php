<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'brand',
        'model_name',
        'slug',
        'image_url',
        'release_year',
        'battery',
        'camera',
        'storage',
        'ram',
        'processor',
        'performance',
        'display',
        'operating_system',
        'specs',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
