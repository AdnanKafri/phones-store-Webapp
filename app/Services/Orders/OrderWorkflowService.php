<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\User;
use App\Services\Notifications\OrderNotificationService;
use App\Services\Wallet\WalletLedgerService;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderWorkflowService
{
    public function __construct(
        private WalletLedgerService $walletLedger,
        private OrderNotificationService $notifications,
    ) {
    }

    public function handleSellerAction(Order $order, string $action, User $seller): array
    {
        if ($action === 'approve') {
            if (! $order->admin_approval) {
                return $this->failure('يجب انتظار موافقة الإدارة أولاً.', 'ORDER_ADMIN_APPROVAL_REQUIRED');
            }

            if ($order->payment_method === 'wallet') {
                $buyer = $order->user;

                if ($buyer->wallet_balance < $order->total_price) {
                    return $this->failure(
                        'رصيد المشتري غير كافي حالياً. لا يمكن إتمام البيع.',
                        'ORDER_BUYER_BALANCE_INSUFFICIENT'
                    );
                }

                try {
                    DB::transaction(function () use ($buyer, $order, $seller) {
                        $this->walletLedger->withdrawForMarketplacePurchase($buyer, $order);
                        $this->walletLedger->depositForMarketplaceSale($seller, $order);

                        $order->seller_approval = true;
                        $order->status = 'approved';
                        $order->save();

                        $order->product->update(['status' => 'sold']);
                    });

                    $this->notifications->notifyBuyerSellerApprovedWithWallet($buyer, $order);
                } catch (Exception $e) {
                    return $this->failure(
                        'حدث خطأ أثناء معالجة العملية: '.$e->getMessage(),
                        'ORDER_WORKFLOW_FAILED'
                    );
                }
            } else {
                $order->seller_approval = true;
                $order->status = 'approved';
                $order->product->update(['status' => 'sold']);
                $order->save();

                $this->notifications->notifyBuyerSellerApprovedWithoutWallet($order);
            }
        } else {
            $order->seller_approval = false;
            $order->status = 'rejected';
            $order->save();

            $this->notifications->notifyBuyerSellerRejected($order);
        }

        return $this->success();
    }

    public function handleAdminAction(Order $order, string $action): array
    {
        if ($order->order_type === 'inventory') {
            return $this->handleInventoryOrderAction($order, $action);
        }

        return $this->handleMarketplaceOrderAction($order, $action);
    }

    private function handleInventoryOrderAction(Order $order, string $action): array
    {
        if ($action === 'approve') {
            if ($order->variant) {
                if ($order->variant->stock_quantity <= 0) {
                    return $this->failure('اللون المختار نفد من المخزون!', 'ORDER_VARIANT_OUT_OF_STOCK');
                }

                $order->variant->decrement('stock_quantity');
            }

            if ($order->payment_method === 'wallet') {
                if ($order->user->wallet_balance < $order->total_price) {
                    return $this->failure(
                        'رصيد المشتري غير كافي لإتمام العملية.',
                        'ORDER_BUYER_BALANCE_INSUFFICIENT'
                    );
                }

                try {
                    $this->walletLedger->withdrawForInventoryPurchase($order->user, $order);
                } catch (Exception $e) {
                    return $this->failure(
                        'حدث خطأ أثناء خصم المبلغ: '.$e->getMessage(),
                        'ORDER_WALLET_DEBIT_FAILED'
                    );
                }
            }

            $order->status = 'approved';
            $order->admin_approval = true;

            $this->notifications->notifyBuyerInventoryOrderApproved($order);
        } elseif ($action === 'ship') {
            $order->status = 'shipping';

            $this->notifications->notifyBuyerInventoryOrderShipped($order);
        } elseif ($action === 'complete') {
            $order->status = 'completed';
        } elseif ($action === 'reject') {
            $order->status = 'rejected';
            $order->admin_approval = false;

            $this->notifications->notifyBuyerInventoryOrderRejected($order);
        }

        $order->save();

        return $this->success();
    }

    private function handleMarketplaceOrderAction(Order $order, string $action): array
    {
        if ($action === 'approve') {
            $order->admin_approval = true;

            if (is_null($order->seller_approval)) {
                $this->notifications->notifySellerAboutApprovedMarketplaceOrder($order);
                $this->notifications->notifyBuyerAdminApprovedMarketplaceOrder($order);
            }
        } elseif ($action === 'reject') {
            $order->admin_approval = false;
            $order->status = 'rejected';

            $this->notifications->notifyBuyerAdminRejectedMarketplaceOrder($order);
        }

        $order->save();

        return $this->success();
    }

    private function success(): array
    {
        return ['success' => true];
    }

    private function failure(string $message, string $code): array
    {
        return [
            'success' => false,
            'message' => $message,
            'code' => $code,
        ];
    }
}
