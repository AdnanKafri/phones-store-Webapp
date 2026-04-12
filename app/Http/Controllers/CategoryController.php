<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\Catalog\CategoryQueryService;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryQueryService $categoryQueryService,
    ) {
    }

    public function index()
    {
        $categories = $this->categoryQueryService->getCategoriesWithProductCount();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        $products = $this->categoryQueryService->getCategoryProducts($category);

        return view('categories.show', compact('category', 'products'));
    }
}
