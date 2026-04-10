<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);
        
        if ($request->has('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->has('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }
        
        $reviews = $query->latest()->paginate(15);
        
        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'product']);
        return view('admin.reviews.show', compact('review'));
    }

    public function destroy(Review $review)
    {
        $review->delete();
        
        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
