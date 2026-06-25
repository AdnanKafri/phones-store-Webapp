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
        $query = trim((string) $request->input('q'));

        if (empty($query)) {
            return redirect()->back();
        }

        return view('search.results', compact('query'));
    }
}
