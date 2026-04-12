<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Orders\OrderQueryService;
use App\Services\Orders\OrderWorkflowService;
use Illuminate\Http\Request;

class SalesOrderController extends ApiController
{
    public function __construct(
        private OrderQueryService $orderQueryService,
        private OrderWorkflowService $orderWorkflowService,
    ) {
    }

    public function index(Request $request)
    {
        $orders = $this->orderQueryService->getApiSellerSalesOrders($request->user()->id);

        return $this->resourceResponse(
            new OrderCollection($orders),
            'Sales orders retrieved successfully.',
        );
    }

    public function approve(Request $request, Order $order)
    {
        return $this->handleAction($request, $order, 'approve', 'Order approved successfully.');
    }

    public function reject(Request $request, Order $order)
    {
        return $this->handleAction($request, $order, 'reject', 'Order rejected successfully.');
    }

    private function handleAction(Request $request, Order $order, string $action, string $message)
    {
        $order = $this->orderQueryService->getApiSalesOrderForSeller($order, $request->user()->id);

        if (! $order) {
            return $this->errorResponse(
                'You are not authorized to manage this sales order.',
                'FORBIDDEN',
                403,
            );
        }

        $result = $this->orderWorkflowService->handleSellerAction($order, $action, $request->user());

        if (! $result['success']) {
            return $this->errorResponse(
                $result['message'],
                $result['code'] ?? 'ORDER_WORKFLOW_FAILED',
                422,
            );
        }

        $order = $this->orderQueryService->loadApiOrder($order->fresh());

        return $this->resourceResponse(
            new OrderResource($order),
            $message,
        );
    }
}
