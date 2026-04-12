<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Catalog\ProductQueryService;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    public function __construct(
        private ProductQueryService $productQueryService,
    ) {
    }

    public function index(Request $request)
    {
        $products = $this->productQueryService->getApiPublicProducts($request->only(['source', 'status']));

        return $this->resourceResponse(
            new ProductCollection($products),
            'Products retrieved successfully.'
        );
    }

    public function show(Product $product)
    {
        $product = $this->productQueryService->loadApiPublicProduct($product);

        return $this->resourceResponse(
            new ProductResource($product),
            'Product retrieved successfully.'
        );
    }
}
