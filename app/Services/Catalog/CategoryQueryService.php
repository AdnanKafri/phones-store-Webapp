<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryQueryService
{
    /**
     * Return categories that have at least one available product,
     * with a count of only those available products.
     */
    public function getCategoriesWithProductCount(): Collection
    {
        $availableScope = $this->availableProductScope();

        return Category::whereHas('products', $availableScope)
            ->withCount(['products' => $availableScope])
            ->orderBy('name')
            ->get();
    }

    public function getApiCategories(): Collection
    {
        return $this->getCategoriesWithProductCount();
    }

    public function getCategoryProducts(Category $category, int $perPage = 12): LengthAwarePaginator
    {
        return Product::where('category_id', $category->id)
            ->where('status', 'available')
            ->with(['seller', 'category'])
            ->paginate($perPage);
    }

    /**
     * Reusable closure that filters products to only "available" status.
     */
    private function availableProductScope(): \Closure
    {
        return function ($query) {
            $query->where('status', 'available');
        };
    }
}
