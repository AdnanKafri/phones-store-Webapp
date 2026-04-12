<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Orders\StoreOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Orders\OrderPlacementService;
use App\Services\Orders\OrderQueryService;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function __construct(
        private OrderQueryService $orderQueryService,
        private OrderPlacementService $orderPlacementService,
    ) {
    }

    public function index(Request $request)
    {
        $orders = $this->orderQueryService->getApiUserOrders($request->user()->id);

        return $this->resourceResponse(
            new OrderCollection($orders),
            'Orders retrieved successfully.',
        );
    }

    public function show(Request $request, Order $order)
    {
        $order = $this->orderQueryService->getApiOrderForBuyer($order, $request->user()->id);

        if (! $order) {
            return $this->errorResponse(
                'You are not authorized to view this order.',
                'FORBIDDEN',
                403,
            );
        }

        return $this->resourceResponse(
            new OrderResource($order),
            'Order retrieved successfully.',
        );
    }

    public function store(StoreOrderRequest $request)
    {
        $result = $this->orderPlacementService->place(
            $request->validated(),
            $request->user(),
            $request->has('color'),
        );

        if (! $result['success']) {
            return $this->errorResponse(
                $result['message'],
                $result['code'] ?? 'ORDER_PLACEMENT_FAILED',
                422,
            );
        }

        $order = $this->orderQueryService->loadApiOrder($result['order']->fresh());

        return $this->resourceResponse(
            new OrderResource($order),
            'Order created successfully.',
            201,
        );
    }
}
