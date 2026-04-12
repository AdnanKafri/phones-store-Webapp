<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use App\Services\Wallet\RechargeRequestService;
use App\Services\Wallet\WalletQueryService;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    public function __construct(
        private WalletQueryService $walletQueryService,
        private RechargeRequestService $rechargeRequestService,
    ) {
    }

    public function index()
    {
        $requests = $this->walletQueryService->getAdminRechargeRequests();

        return view('admin.payment_requests.index', compact('requests'));
    }

    public function update(Request $request, PaymentRequest $paymentRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $result = $this->rechargeRequestService->processRechargeRequest($paymentRequest, $validated['status']);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', 'Request status updated.');
    }
}
