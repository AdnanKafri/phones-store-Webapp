<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::whereIn('status', ['available', 'sold'])
            ->with(['category', 'seller'])
            ->latest()
            ->take(10)
            ->get();

        $requests = \App\Models\DeviceRequest::where('status', 'approved')
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        // Merge and sort by date
        $feedItems = $products->concat($requests)->sortByDesc('created_at');

        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        return view('home', compact('feedItems', 'categories'));
    }
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->back();
        }

        $products = Product::where('status', 'available')
            ->where(function($q) use ($query) {
                $q->where('brand', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%")
                  ->orWhere('category_id', function($q) use ($query) { 
                      $q->select('id')->from('categories')->where('name', 'like', "%{$query}%");
                  });
            })
            ->with(['category', 'seller'])
            ->latest()
            ->get();

        $requests = \App\Models\DeviceRequest::where('status', 'approved')
            ->where(function($q) use ($query) {
                $q->where('brand', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%");
            })
            ->with('user')
            ->latest()
            ->get();

        return view('search.results', compact('products', 'requests', 'query'));
    }
}
