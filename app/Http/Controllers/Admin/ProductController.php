<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['user', 'category']);
        
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }
        
        $products = $query->latest()->paginate(15);
        $categories = Category::all();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function inventory(Request $request)
    {
        $query = Product::where('source', 'inventory')->with(['category', 'variants']);

        if ($request->has('search')) {
            $term = $request->search;
            $query->where(function($q) use ($term) {
                $q->where('brand', 'like', "%{$term}%")
                  ->orWhere('model', 'like', "%{$term}%");
            });
        }

        $products = $query->latest()->paginate(20);
        return view('admin.products.inventory', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $users = User::all();
        return view('admin.products.create', compact('categories', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'name' => 'nullable|string|max:255', // Optional, can be generated
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,used',
            'condition_notes' => 'nullable|string',
            'status' => 'required|in:available,sold,hidden',
            'source' => 'required|in:inventory,user',
            'color' => 'nullable|string', // For simple products
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'nullable|array',
            'variants.*.color_name' => 'required_with:variants|string',
            'variants.*.stock_quantity' => 'required_with:variants|integer|min:0',
        ]);
        
        $validated['name'] = $request->input('name', $validated['brand'] . ' ' . $validated['model']);
        $validated['slug'] = Str::slug($validated['name']) . '-' . time();
        
        $product = Product::create($validated);

        // Handle Variants for Inventory
        if ($request->source === 'inventory' && $request->has('variants')) {
            foreach ($request->variants as $variantData) {
                $product->variants()->create([
                    'color_name' => $variantData['color_name'],
                    'stock_quantity' => $variantData['stock_quantity'],
                    // 'color_code' can be added if UI supports it
                ]);
            }
        }
        
        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => ($index == 0),
                ]);
            }
        }
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['user', 'category', 'images', 'reviews']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants']);
        $categories = Category::all();
        $users = User::all();
        return view('admin.products.edit', compact('product', 'categories', 'users'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,used',
            'status' => 'required|in:available,sold,hidden,pending,rejected',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image' => 'nullable|integer',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']) . '-' . $product->id;
        
        $product->update($validated);
        
        // Handle new image uploads
        if ($request->hasFile('images')) {
            $primaryIndex = $request->input('primary_image', 0);
            
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => ($index == $primaryIndex),
                ]);
            }
        }
        
        // Handle Product Variants (for inventory products only)
        if ($product->source === 'inventory' && $request->has('variants')) {
            foreach ($request->variants as $variantData) {
                if (isset($variantData['id'])) {
                    // Update existing variant
                    $variant = \App\Models\ProductVariant::find($variantData['id']);
                    if ($variant && $variant->product_id === $product->id) {
                        $variant->update([
                            'color_name' => $variantData['color_name'],
                            'color_code' => $variantData['color_code'],
                            'stock_quantity' => $variantData['stock_quantity'],
                        ]);
                    }
                } else {
                    // Create new variant
                    $product->variants()->create([
                        'color_name' => $variantData['color_name'],
                        'color_code' => $variantData['color_code'],
                        'stock_quantity' => $variantData['stock_quantity'],
                    ]);
                }
            }
        }
        
        // Handle variant deletions
        if ($request->has('variants_to_delete')) {
            \App\Models\ProductVariant::whereIn('id', $request->variants_to_delete)
                ->where('product_id', $product->id)
                ->delete();
        }
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete all product images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        
        return back()->with('success', 'Image deleted successfully.');
    }

    public function setPrimaryImage(ProductImage $image)
    {
        // Remove primary flag from all images of this product
        ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
        
        // Set this image as primary
        $image->update(['is_primary' => true]);
        
        return back()->with('success', 'Primary image updated successfully.');
    }
}
