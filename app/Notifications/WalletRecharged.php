<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WalletRecharged extends Notification
{
    use Queueable;

    protected $amount;
    protected $reason;

    public function __construct($amount, $reason)
    {
        $this->amount = $amount;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Wallet Recharged',
            'message' => 'Your wallet has been recharged with $' . number_format($this->amount, 2),
            'amount' => $this->amount,
            'reason' => $this->reason,
            'type' => 'wallet_recharge',
        ];
    }
}
