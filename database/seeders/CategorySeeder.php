<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'iPhone', 'slug' => 'iphone', 'description' => 'Apple iPhone devices'],
            ['name' => 'Samsung', 'slug' => 'samsung', 'description' => 'Samsung Galaxy devices'],
            ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'description' => 'Xiaomi phones'],
            ['name' => 'Huawei', 'slug' => 'huawei', 'description' => 'Huawei devices'],
            ['name' => 'Oppo', 'slug' => 'oppo', 'description' => 'Oppo smartphones'],
            ['name' => 'Vivo', 'slug' => 'vivo', 'description' => 'Vivo phones'],
            ['name' => 'OnePlus', 'slug' => 'oneplus', 'description' => 'OnePlus devices'],
            ['name' => 'Google Pixel', 'slug' => 'google-pixel', 'description' => 'Google Pixel phones'],
            ['name' => 'Tablets', 'slug' => 'tablets', 'description' => 'Tablets and iPads'],
            ['name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Phone accessories'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
