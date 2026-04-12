<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryQueryService
{
    public function getCategoriesWithProductCount(): Collection
    {
        return Category::withCount('products')->get();
    }

    public function getApiCategories(): Collection
    {
        return $this->getCategoriesWithProductCount();
    }

    public function getCategoryProducts(Category $category, int $perPage = 12): LengthAwarePaginator
    {
        return Product::where('category_id', $category->id)
            ->with(['seller', 'category'])
            ->paginate($perPage);
    }
}
