<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $fillable = [
        'user_id',
        'payment_method',
        'amount',
        'reference_number',
        'notes',
        'status',
        'admin_notes',
        'type', // deposit/withdraw
        'proof_image',
    ];

    const ALLOWED_PAYMENT_METHODS = [
        "Cash",
        "Heram Transfer",
        "Syriatel Cash",
        "Sham Cash",
        "MTN Cash",
        "Other"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
