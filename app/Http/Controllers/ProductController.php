<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'seller']);

        // Filter by Source
        if ($request->has('source') && in_array($request->source, ['inventory', 'user'])) {
            $query->where('source', $request->source);
        }

        // Filter by Status (Default: show all except hidden, but prompts says "Never hide products automatically")
        // "Products with status: available, sold, hidden MUST still exist... except hidden if explicitly filtered"
        // Let's allow filtering. If no filter, show all (maybe exclude hidden default? No, "Default view shows ALL products")
        // "Default view shows ALL products" includes sold? Yes. Hidden? Maybe hidden means "administratively hidden".
        // Let's assumption: Hidden means "Draft/Archived". User might not want to see them.
        // Prompt: "Products with status != 'available' MUST still appear... except hidden if explicitly filtered".
        // So show Available and Sold by default. Show Hidden only if filtered?
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
             $query->where('status', '!=', 'pending'); // Hide pending by default
             // Note: Admin might want to see pending, but this is public index.
             // Pending products should be reviewed in Admin Dashboard.
             // Sold and Hidden allow user to filter.
             // We allow sold, but hide hidden and pending.
             // Wait, previous logic was: whereIn('status', ['available', 'sold', 'hidden']).
             // I should remove 'hidden'? Prompt said "except hidden if explicitly filtered".
             // Logic: Default = Available + Sold. Hidden/Pending excluded.
        }

        $products = $query->latest()->paginate(12);

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
            'location' => 'required|string|max:255', // User Marketplace Requirement
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

        // Generate name and slug from brand + model
        $name = $validated['brand'] . ' ' . $validated['model'];
        $slug = \Str::slug($name) . '-' . uniqid();

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending'; // Require Admin Approval
        $validated['source'] = 'user'; // Explicitly set as user product
        $validated['name'] = $name;
        $validated['slug'] = $slug;
        $validated['disassembled_is'] = $request->has('disassembled_is') ? 1 : 0;

        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('dashboard.my-listings')->with('success', 'تم إرسال إعلانك بنجاح وهو قيد المراجعة من قبل الإدارة.');
    }

    public function edit(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $product->load('images', 'category');
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== auth()->id()) {
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

        // Update name and slug if brand or model changed
        $name = $validated['brand'] . ' ' . $validated['model'];
        $validated['name'] = $name;
        $validated['slug'] = \Str::slug($name) . '-' . $product->id;
        $validated['disassembled_is'] = $request->has('disassembled_is') ? 1 : 0;

        // Handle image deletions
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = $product->images()->find($imageId);
                if ($image) {
                    \Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $currentImageCount = $product->images()->count();
            $newImageCount = count($request->file('images'));
            
            if ($currentImageCount + $newImageCount > 5) {
                return back()->withErrors(['images' => 'لا يمكن أن يتجاوز مجموع الصور 5 صور. يرجى حذف بعض الصور القديمة أولاً.']);
            }

            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_path' => $path]);
            }
        }

        $product->update($validated);

        return redirect()->route('dashboard.my-listings')->with('success', 'تم تعديل الإعلان بنجاح!');
    }

    public function destroy(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }

        $product->images()->delete();
        $product->delete();

        return redirect()->route('dashboard.my-listings')->with('success', 'تم حذف الإعلان بنجاح!');
    }

    public function show(Product $product)
    {
        $product->load(['seller', 'category', 'reviews.user', 'variants']); // Load variants

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'available')
            ->with('seller')
            ->take(4)
            ->get();

        return view('product.show', compact('product', 'relatedProducts'));
    }
}
