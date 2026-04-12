<?php

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\PaymentRequestCollection;
use App\Http\Resources\PaymentRequestResource;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionCollection;
use App\Http\Requests\Api\V1\Wallet\StoreRechargeRequestRequest;
use App\Services\Wallet\RechargeRequestService;
use App\Services\Wallet\WalletQueryService;
use Illuminate\Http\Request;

class WalletController extends ApiController
{
    public function __construct(
        private WalletQueryService $walletQueryService,
        private RechargeRequestService $rechargeRequestService,
    ) {
    }

    public function show(Request $request)
    {
        $summary = $this->walletQueryService->getUserWalletSummary($request->user());

        return $this->resourceResponse(
            new WalletResource($summary),
            'Wallet retrieved successfully.'
        );
    }

    public function transactions(Request $request)
    {
        $transactions = $this->walletQueryService->getUserTransactions($request->user()->id);

        return $this->resourceResponse(
            new WalletTransactionCollection($transactions),
            'Wallet transactions retrieved successfully.'
        );
    }

    public function rechargeRequests(Request $request)
    {
        $paymentRequests = $this->walletQueryService->getUserRechargeRequests($request->user()->id);

        return $this->resourceResponse(
            new PaymentRequestCollection($paymentRequests),
            'Recharge requests retrieved successfully.'
        );
    }

    public function storeRechargeRequest(StoreRechargeRequestRequest $request)
    {
        $paymentRequest = $this->rechargeRequestService->createRechargeRequest(
            $request->validated(),
            $request->user(),
            $request->hasFile('proof')
        );

        return $this->resourceResponse(
            new PaymentRequestResource($paymentRequest),
            'Recharge request created successfully.',
            201
        );
    }
}
