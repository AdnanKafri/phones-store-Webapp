<?php

namespace App\Services\Notifications;

use App\Models\PaymentRequest;
use App\Models\User;
use App\Services\NotificationService;

class WalletNotificationService
{
    public function notifyAdminsAboutRechargeRequest(User $user, PaymentRequest $paymentRequest): void
    {
        NotificationService::broadcastToAdmins(
            'New Recharge Request',
            "User {$user->name} requested recharge of \${$paymentRequest->amount}.",
            'wallet'
        );
    }

    public function notifyUserRechargeApproved(PaymentRequest $paymentRequest): void
    {
        NotificationService::send(
            $paymentRequest->user_id,
            'تم قبول الشحن',
            "تم شحن محفظتك بمبلغ \${$paymentRequest->amount} بنجاح.",
            'wallet'
        );
    }

    public function notifyUserRechargeRejected(PaymentRequest $paymentRequest): void
    {
        NotificationService::send(
            $paymentRequest->user_id,
            'تم رفض الشحن',
            'عذراً، تم رفض طلب شحن المحفظة الخاص بك. يرجى التحقق من البيانات.',
            'wallet'
        );
    }
}
