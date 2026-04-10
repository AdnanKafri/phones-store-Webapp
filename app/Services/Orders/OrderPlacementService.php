<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Notifications\OrderNotificationService;

class OrderPlacementService
{
    public function __construct(
        private OrderNotificationService $notifications,
    ) {
    }

    public function place(array $validated, User $buyer, bool $hasColor = false): array
    {
        $product = Product::findOrFail($validated['product_id']);

        if ($product->user_id === $buyer->id) {
            return $this->failure('لا يمكنك شراء منتجك الخاص.');
        }

        if ($product->status !== 'available') {
            return $this->failure('هذا المنتج لم يعد متوفراً.');
        }

        if ($product->source === 'user') {
            $existing = Order::where('user_id', $buyer->id)
                ->where('product_id', $product->id)
                ->where('status', 'pending')
                ->first();

            if ($existing) {
                return $this->failure('لديك طلب معلق مسبقاً لهذا المنتج.');
            }
        }

        if ($validated['payment_method'] === 'wallet' && $buyer->wallet_balance < $product->price) {
            return $this->failure('رصيد المحفظة غير كافي. يرجى شحن الرصيد أو اختيار طريقة دفع أخرى.');
        }

        $order = new Order();
        $order->user_id = $buyer->id;
        $order->product_id = $product->id;
        $order->total_price = $product->price;
        $order->shipping_address = $validated['shipping_address'];
        $order->status = 'pending';
        $order->payment_method = $validated['payment_method'];

        if ($product->source === 'inventory') {
            $order->order_type = 'inventory';
            $order->admin_approval = null;
            $order->seller_approval = true;

            if ($hasColor) {
                $order->product_variant_id = $validated['color'];
                $variant = ProductVariant::find($validated['color']);

                if ($variant && $variant->stock_quantity <= 0) {
                    return $this->failure('اللون المختار غير متوفر حالياً.');
                }
            }
        } else {
            $order->order_type = 'user';
            $order->seller_approval = null;
            $order->admin_approval = null;
        }

        $order->save();

        if ($product->source === 'user') {
            $this->notifications->notifySellerAboutNewUserOrder($product);
        }

        if ($validated['payment_method'] === 'stripe') {
            $this->notifications->notifyBuyerAboutStripePayment($buyer);
        }

        $this->notifications->notifyAdminsAboutNewOrder($order, $buyer);
        $this->notifications->notifyBuyerOrderReceived($buyer, $order);

        return [
            'success' => true,
            'order' => $order,
        ];
    }

    private function failure(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
        ];
    }
}
