<?php

namespace App\Http\Controllers\Api\V1\Listings;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Listings\StoreListingRequest;
use App\Http\Requests\Api\V1\Listings\UpdateListingRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Catalog\ProductQueryService;
use App\Services\Listings\ListingService;
use Illuminate\Http\Request;

class ListingController extends ApiController
{
    public function __construct(
        private ProductQueryService $productQueryService,
        private ListingService $listingService,
    ) {
    }

    public function index(Request $request)
    {
        $products = $this->productQueryService->getApiUserListings($request->user()->id);

        return $this->resourceResponse(
            new ProductCollection($products),
            'Listings retrieved successfully.'
        );
    }

    public function store(StoreListingRequest $request)
    {
        $product = $this->listingService->createUserListing($request->validated(), $request->user());
        $product = $this->productQueryService->loadApiPublicProduct($product->fresh());

        return $this->resourceResponse(
            new ProductResource($product),
            'Listing created successfully.',
            201
        );
    }

    public function update(UpdateListingRequest $request, Product $product)
    {
        $result = $this->listingService->updateUserListing(
            $product,
            $request->validated(),
            $request->user(),
            $request->file('images', []),
            $request->validated()['delete_images'] ?? [],
        );

        if (! $result['success']) {
            return $this->errorResponse(
                $result['message'],
                $result['code'] ?? 'LISTING_UPDATE_FAILED',
                ($result['code'] ?? null) === 'FORBIDDEN' ? 403 : 422
            );
        }

        $product = $this->productQueryService->loadApiPublicProduct($result['product']->fresh());

        return $this->resourceResponse(
            new ProductResource($product),
            'Listing updated successfully.'
        );
    }

    public function destroy(Request $request, Product $product)
    {
        $result = $this->listingService->deleteUserListing($product, $request->user());

        if (! $result['success']) {
            return $this->errorResponse(
                $result['message'],
                $result['code'] ?? 'LISTING_DELETE_FAILED',
                403
            );
        }

        return $this->successResponse(null, 'Listing deleted successfully.');
    }
}
