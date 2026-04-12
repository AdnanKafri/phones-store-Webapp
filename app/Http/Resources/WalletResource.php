<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this['user_id'],
            'balance' => (float) $this['balance'],
            'transactions_count' => (int) $this['transactions_count'],
            'recharge_requests_count' => (int) $this['recharge_requests_count'],
            'pending_recharge_requests_count' => (int) $this['pending_recharge_requests_count'],
        ];
    }
}
