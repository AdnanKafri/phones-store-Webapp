<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DeviceRequestResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\DeviceRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class GeneralController extends ApiController
{
    public function home()
    {
        $products = Product::whereIn('status', ['available', 'sold'])
            ->with(['category', 'seller', 'images', 'variants'])
            ->latest()
            ->take(10)
            ->get();

        $requests = DeviceRequest::where('status', 'approved')
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        return $this->successResponse([
            'categories' => CategoryResource::collection($categories),
            'featured_products' => ProductResource::collection($products),
            'device_requests' => DeviceRequestResource::collection($requests),
        ], 'Home feed retrieved successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return $this->successResponse([
                'products' => [],
                'device_requests' => [],
            ], 'Empty search query.');
        }

        $products = Product::where('status', 'available')
            ->where(function($q) use ($query) {
                $q->where('brand', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%")
                  ->orWhere('category_id', function($q) use ($query) { 
                      $q->select('id')->from('categories')->where('name', 'like', "%{$query}%");
                  });
            })
            ->with(['category', 'seller', 'images', 'variants'])
            ->latest()
            ->paginate(15);

        $requests = DeviceRequest::where('status', 'approved')
            ->where(function($q) use ($query) {
                $q->where('brand', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%");
            })
            ->with('user')
            ->latest()
            ->paginate(15);

        return $this->successResponse([
            'products' => [
                'data' => ProductResource::collection($products->items()),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'total' => $products->total(),
                    'has_more_pages' => $products->hasMorePages(),
                ]
            ],
            'device_requests' => [
                'data' => DeviceRequestResource::collection($requests->items()),
                'meta' => [
                    'current_page' => $requests->currentPage(),
                    'last_page' => $requests->lastPage(),
                    'total' => $requests->total(),
                    'has_more_pages' => $requests->hasMorePages(),
                ]
            ],
            'query' => $query,
        ], 'Search results retrieved successfully.');
    }
}
