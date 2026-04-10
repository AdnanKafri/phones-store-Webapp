<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    public function index()
    {
        $requests = PaymentRequest::with('user')->latest()->paginate(20);
        return view('admin.payment_requests.index', compact('requests'));
    }

    public function update(Request $request, PaymentRequest $paymentRequest)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);

        if ($paymentRequest->status !== 'pending') {
            return back()->with('error', 'Request already processed.');
        }

        if ($request->status === 'approved') {
            $paymentRequest->update(['status' => 'approved']);
            
            // Credit User Wallet
            $paymentRequest->user->deposit(
                $paymentRequest->amount, 
                'deposit', 
                "Wallet Recharge (Ref: {$paymentRequest->id})"
            );

            \App\Services\NotificationService::send(
                $paymentRequest->user_id, 
                'تم قبول الشحن', 
                "تم شحن محفظتك بمبلغ \${$paymentRequest->amount} بنجاح.", 
                'wallet'
            );
        } else {
            $paymentRequest->update(['status' => 'rejected']);
            
            \App\Services\NotificationService::send(
                $paymentRequest->user_id, 
                'تم رفض الشحن', 
                "عذراً، تم رفض طلب شحن المحفظة الخاص بك. يرجى التحقق من البيانات.", 
                'wallet'
            );
        }

        return back()->with('success', 'Request status updated.');
    }
}
