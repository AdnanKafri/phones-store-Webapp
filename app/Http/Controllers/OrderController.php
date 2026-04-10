<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Orders\OrderPlacementService;
use App\Services\Orders\OrderQueryService;
use App\Services\Orders\OrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private OrderPlacementService $orderPlacementService,
        private OrderWorkflowService $orderWorkflowService,
        private OrderQueryService $orderQueryService,
    ) {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'shipping_address' => 'required|string|max:1000',
            'color' => 'nullable|exists:product_variants,id',
            'payment_method' => 'required|in:wallet,stripe,cod', // cod = Cash on Delivery
        ]);

        $result = $this->orderPlacementService->place($validated, Auth::user(), $request->has('color'));

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('orders.confirmation', $result['order']->id)->with('success', 'تم إرسال طلبك بنجاح.');
    }

    public function index()
    {
        $orders = $this->orderQueryService->getUserOrders(Auth::id());
            
        return view('dashboard.orders.index', compact('orders'));
    }

    public function sales()
    {
        $orders = $this->orderQueryService->getSellerSalesOrders(Auth::id());

        return view('dashboard.orders.sales', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Seller approval logic
        if ($order->product->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        $result = $this->orderWorkflowService->handleSellerAction($order, $validated['action'], Auth::user());

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }

    public function confirmation(Order $order)
    {
        // Allow Buyer OR Admin to view
        if (! $this->orderQueryService->canViewConfirmation($order, Auth::user())) {
            abort(403);
        }

        $order = $this->orderQueryService->loadConfirmationOrder($order);

        return view('dashboard.orders.confirmation', compact('order'));
    }
}
