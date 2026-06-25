<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Catalog\ProductQueryService;
use App\Services\Devices\DeviceCatalogService;
use App\Services\Listings\ListingService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductQueryService $productQueryService,
        private DeviceCatalogService $deviceCatalogService,
        private ListingService $listingService,
    ) {
    }

    public function index(Request $request)
    {
        $products = $this->productQueryService->getPublicProducts($request->only(['source', 'status']));

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,used',
            'description' => 'nullable|string|max:5000',
            'condition_notes' => 'nullable|string|max:1000',
            'accessories' => 'nullable|string|max:2000',
            'disassembled_is' => 'nullable|boolean',
            'location' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120'
        ], [
            'brand.required' => 'يرجى إدخال ماركة الهاتف',
            'model.required' => 'يرجى إدخال موديل الهاتف',
            'category_id.required' => 'يرجى اختيار القسم',
            'price.required' => 'يرجى إدخال السعر',
            'price.min' => 'يجب أن يكون السعر أكبر من 0',
            'condition.required' => 'يرجى تحديد حالة الهاتف',
            'location.required' => 'يرجى إدخال المدينة/المحافظة',
            'color.required' => 'يرجى تحديد اللون',
            'images.required' => 'يرجى رفع صورة واحدة على الأقل',
            'images.min' => 'يرجى رفع صورة واحدة على الأقل',
            'images.max' => 'يمكنك رفع 5 صور كحد أقصى',
            'images.*.image' => 'يجب أن تكون الملفات صوراً',
            'images.*.max' => 'حجم الصورة يجب ألا يتجاوز 5 ميجابايت'
        ]);

        $this->listingService->createUserListing($validated, $request->user());

        return redirect()->route('dashboard.my-listings')->with('success', 'تم إرسال إعلانك بنجاح وهو قيد المراجعة من قبل الإدارة.');
    }

    public function edit(Product $product)
    {
        if (! $this->listingService->canManage($product, auth()->user())) {
            abort(403);
        }

        $product = $this->productQueryService->loadListingForEdit($product);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if (! $this->listingService->canManage($product, auth()->user())) {
            abort(403);
        }

        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,used',
            'color' => 'required|string|max:50',
            'status' => 'nullable|in:available,sold,hidden,pending,rejected',
            'description' => 'nullable|string|max:5000',
            'defects' => 'nullable|string|max:2000',
            'accessories' => 'nullable|string|max:2000',
            'disassembled_is' => 'nullable|boolean',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_images,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120'
        ], [
            'brand.required' => 'يرجى إدخال ماركة الهاتف',
            'model.required' => 'يرجى إدخال موديل الهاتف',
            'category_id.required' => 'يرجى اختيار القسم',
            'price.required' => 'يرجى إدخال السعر',
            'price.min' => 'يجب أن يكون السعر أكبر من 0',
            'condition.required' => 'يرجى تحديد حالة الهاتف',
            'images.max' => 'يمكنك رفع 5 صور كحد أقصى',
            'images.*.max' => 'حجم الصورة يجب ألا يتجاوز 5 ميجابايت'
        ]);

        $result = $this->listingService->updateUserListing(
            $product,
            $validated,
            $request->user(),
            $request->file('images', []),
            $validated['delete_images'] ?? [],
        );

        if (! $result['success']) {
            if (($result['code'] ?? null) === 'FORBIDDEN') {
                abort(403);
            }

            return back()->withErrors(['images' => $result['message']]);
        }

        return redirect()->route('dashboard.my-listings')->with('success', 'تم تعديل الإعلان بنجاح!');
    }

    public function destroy(Product $product)
    {
        $result = $this->listingService->deleteUserListing($product, auth()->user());

        if (! $result['success']) {
            abort(403);
        }

        return redirect()->route('dashboard.my-listings')->with('success', 'تم حذف الإعلان بنجاح!');
    }

    public function show(Product $product)
    {
        $product = $this->productQueryService->loadPublicProduct($product);
        $relatedProducts = $this->productQueryService->getRelatedProducts($product);
        $compareDevice = $this->deviceCatalogService->resolveProductDevice($product);

        return view('product.show', compact('product', 'relatedProducts', 'compareDevice'));
    }
}
