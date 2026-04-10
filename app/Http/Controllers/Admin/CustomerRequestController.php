<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CustomerRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerRequest::with(['user', 'category']);
        
        if ($request->has('search')) {
            $query->where('device_name', 'like', "%{$request->search}%");
        }
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }
        
        $requests = $query->latest()->paginate(15);
        $categories = Category::all();
        
        return view('admin.customer-requests.index', compact('requests', 'categories'));
    }

    public function show(CustomerRequest $customerRequest)
    {
        $customerRequest->load(['user', 'category']);
        return view('admin.customer-requests.show', compact('customerRequest'));
    }

    public function update(Request $request, CustomerRequest $customerRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,matched,cancelled',
        ]);
        
        $customerRequest->update($validated);
        
        return back()->with('success', 'Request status updated successfully.');
    }

    public function destroy(CustomerRequest $customerRequest)
    {
        $customerRequest->delete();
        
        return redirect()->route('admin.customer-requests.index')
            ->with('success', 'Customer request deleted successfully.');
    }
}
