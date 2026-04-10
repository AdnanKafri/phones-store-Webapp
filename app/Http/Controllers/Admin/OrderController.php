<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Orders\OrderQueryService;
use App\Services\Orders\OrderWorkflowService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderQueryService $orderQueryService,
        private OrderWorkflowService $orderWorkflowService,
    )
    {
        // Simple role check. Middleware recommended for production but this works for now.
        if (auth()->check() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $orders = $this->orderQueryService->getAdminOrders(
            $request->has('type') ? $request->type : null
        );

        return view('admin.orders.index', compact('orders'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,ship,complete'
        ]);

        $action = $validated['action'];

        $result = $this->orderWorkflowService->handleAdminAction($order, $action);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', 'Order updated successfully.');
    }
}
