<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductPurchased extends Notification
{
    use Queueable;

    protected $transaction;
    protected $role; // 'buyer' or 'seller'

    public function __construct($transaction, $role)
    {
        $this->transaction = $transaction;
        $this->role = $role;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $productName = $this->transaction->product->name;
        $amount = $this->transaction->amount;
        
        $message = $this->role === 'buyer' 
            ? "You successfully purchased '{$productName}' for \${$amount}."
            : "Your product '{$productName}' was sold for \${$amount}.";

        return [
            'title' => 'Product Transaction',
            'message' => $message,
            'transaction_id' => $this->transaction->id,
            'product_name' => $productName,
            'role' => $this->role,
            'type' => 'product_transaction',
        ];
    }
}
