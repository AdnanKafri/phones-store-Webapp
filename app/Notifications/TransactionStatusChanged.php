<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransactionStatusChanged extends Notification
{
    use Queueable;

    protected $transaction;
    protected $status;

    public function __construct($transaction, $status)
    {
        $this->transaction = $transaction;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Transaction Updated',
            'message' => "Transaction #{$this->transaction->id} for '{$this->transaction->product->name}' is now {$this->status}.",
            'transaction_id' => $this->transaction->id,
            'status' => $this->status,
            'type' => 'transaction_status',
        ];
    }
}
