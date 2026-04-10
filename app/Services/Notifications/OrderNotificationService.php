<?php

namespace App\Services\Notifications;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\NotificationService;

class OrderNotificationService
{
    public function notifySellerAboutNewUserOrder(Product $product): void
    {
        NotificationService::send(
            $product->user_id,
            'طلب جديد',
            "لديك طلب شراء جديد: {$product->brand} {$product->model}. يرجى مراجعة الطلبات.",
            'seller_dashboard'
        );
    }

    public function notifyBuyerAboutStripePayment(User $buyer): void
    {
        NotificationService::send(
            $buyer->id,
            'Payment Successful',
            'Stripe payment simulation successful.',
            'system'
        );
    }

    public function notifyAdminsAboutNewOrder(Order $order, User $buyer): void
    {
        NotificationService::broadcastToAdmins(
            'New Order',
            "Order #{$order->id} placed by {$buyer->name}."
        );
    }

    public function notifyBuyerOrderReceived(User $buyer, Order $order): void
    {
        NotificationService::send(
            $buyer->id,
            'تم استلام طلبك',
            "تم استلام طلبك #{$order->id} بنجاح. يمكنك متابعة التفاصيل والفاتورة.",
            'order',
            ['url' => route('orders.confirmation', $order->id)]
        );
    }

    public function notifyBuyerSellerApprovedWithWallet(User $buyer, Order $order): void
    {
        NotificationService::send(
            $buyer->id,
            'تم تأكيد الشراء!',
            "وافق البائع على طلبك #{$order->id}. تم خصم المبلغ وإتمام العملية بنجاح.",
            'order',
            ['url' => route('orders.confirmation', $order->id)]
        );
    }

    public function notifyBuyerSellerApprovedWithoutWallet(Order $order): void
    {
        NotificationService::send(
            $order->user_id,
            'تم تأكيد الشراء',
            "وافق البائع على طلبك #{$order->id}. يرجى التواصل معه لإتمام التسليم.",
            'order',
            ['url' => route('orders.confirmation', $order->id)]
        );
    }

    public function notifyBuyerSellerRejected(Order $order): void
    {
        NotificationService::send(
            $order->user_id,
            'تم رفض الطلب',
            "عذراً، اعتذر البائع عن قبول طلبك #{$order->id}.",
            'order'
        );
    }

    public function notifyBuyerInventoryOrderApproved(Order $order): void
    {
        NotificationService::send(
            $order->user_id,
            'تم قبول طلبك',
            "تمت الموافقة على طلبك #{$order->id} وجاري التحضير.",
            'order'
        );
    }

    public function notifyBuyerInventoryOrderShipped(Order $order): void
    {
        NotificationService::send(
            $order->user_id,
            'تم شحن طلبك',
            "طلبك #{$order->id} في الطريق إليك.",
            'order'
        );
    }

    public function notifyBuyerInventoryOrderRejected(Order $order): void
    {
        NotificationService::send(
            $order->user_id,
            'تم رفض الطلب',
            "عذراً، تم رفض طلبك #{$order->id}. تواصل مع الإدارة للتفاصيل.",
            'order'
        );
    }

    public function notifySellerAboutApprovedMarketplaceOrder(Order $order): void
    {
        $seller = $order->product->seller;

        if (! $seller) {
            return;
        }

        NotificationService::send(
            $seller->id,
            'طلب شراء جديد',
            "هناك طلب شراء جديد لمنتجك {$order->product->brand} {$order->product->model}. يرجى الموافقة عليه لإتمام البيع.",
            'action',
            ['url' => route('dashboard.sales')]
        );
    }

    public function notifyBuyerAdminApprovedMarketplaceOrder(Order $order): void
    {
        NotificationService::send(
            $order->user_id,
            'موافقة الإدارة',
            "تمت موافقة الإدارة على طلبك #{$order->id}. بانتظار موافقة البائع لإتمام العملية.",
            'order'
        );
    }

    public function notifyBuyerAdminRejectedMarketplaceOrder(Order $order): void
    {
        NotificationService::send(
            $order->user_id,
            'تم رفض الطلب',
            "عذراً، تم رفض طلبك #{$order->id} من قبل الإدارة.",
            'order'
        );
    }
}
