<?php

namespace App\Services\Wallet;

use App\Models\PaymentRequest;
use App\Models\User;
use App\Services\Notifications\WalletNotificationService;

class RechargeRequestService
{
    public function __construct(
        private WalletLedgerService $walletLedgerService,
        private WalletNotificationService $walletNotificationService,
    ) {
    }

    public function createRechargeRequest(array $validated, User $user, bool $hasProof = false): PaymentRequest
    {
        $paymentRequest = PaymentRequest::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'type' => 'deposit',
            'status' => 'pending',
            'payment_method' => $validated['method'],
            'proof_image' => $hasProof ? $validated['proof']->store('payment_proofs', 'public') : null,
        ]);

        $this->walletNotificationService->notifyAdminsAboutRechargeRequest($user, $paymentRequest);

        return $paymentRequest;
    }

    public function processRechargeRequest(PaymentRequest $paymentRequest, string $status): array
    {
        if ($paymentRequest->status !== 'pending') {
            return [
                'success' => false,
                'message' => 'Request already processed.',
                'code' => 'PAYMENT_REQUEST_ALREADY_PROCESSED',
            ];
        }

        if ($status === 'approved') {
            $paymentRequest->update(['status' => 'approved']);
            $paymentRequest->loadMissing('user');

            $this->walletLedgerService->depositForRechargeRequest($paymentRequest);
            $this->walletNotificationService->notifyUserRechargeApproved($paymentRequest);
        } else {
            $paymentRequest->update(['status' => 'rejected']);
            $this->walletNotificationService->notifyUserRechargeRejected($paymentRequest);
        }

        return [
            'success' => true,
            'payment_request' => $paymentRequest,
        ];
    }
}
