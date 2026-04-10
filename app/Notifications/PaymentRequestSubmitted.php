<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PaymentRequestSubmitted extends Notification
{
    use Queueable;

    protected $paymentRequest;

    public function __construct($paymentRequest)
    {
        $this->paymentRequest = $paymentRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Payment Request',
            'message' => 'User ' . $this->paymentRequest->user->name . ' submitted a payment request of $' . $this->paymentRequest->amount,
            'link' => route('admin.payment-requests.show', $this->paymentRequest->id),
            'type' => 'payment_request_submitted',
        ];
    }
}
