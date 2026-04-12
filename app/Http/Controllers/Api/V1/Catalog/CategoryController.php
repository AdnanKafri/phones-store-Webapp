<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CategoryCollection;
use App\Services\Catalog\CategoryQueryService;

class CategoryController extends ApiController
{
    public function __construct(
        private CategoryQueryService $categoryQueryService,
    ) {
    }

    public function index()
    {
        $categories = $this->categoryQueryService->getApiCategories();

        return $this->resourceResponse(
            new CategoryCollection($categories),
            'Categories retrieved successfully.'
        );
    }
}
