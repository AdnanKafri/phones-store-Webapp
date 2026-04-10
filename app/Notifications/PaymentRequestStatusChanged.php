<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentRequestStatusChanged extends Notification
{
    use Queueable;

    protected $paymentRequest;
    protected $status;

    public function __construct($paymentRequest, $status)
    {
        $this->paymentRequest = $paymentRequest;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Payment Request ' . ucfirst($this->status),
            'message' => 'Your payment request for $' . $this->paymentRequest->amount . ' has been ' . $this->status,
            'amount' => $this->paymentRequest->amount,
            'status' => $this->status,
            'type' => 'payment_request_status',
        ];
    }
}
