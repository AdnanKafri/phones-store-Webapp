<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'location',
        'status',
        'password',
        'role',
        'wallet_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }



    public function purchasesAsBuyer()
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    public function salesAsSeller()
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    public function paymentRequests()
    {
        return $this->hasMany(PaymentRequest::class);
    }



    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function deposit($amount, $reason = '', $description = null)
    {
        $before = $this->wallet_balance;
        $this->increment('wallet_balance', $amount);
        $after = $this->wallet_balance;

        return $this->walletTransactions()->create([
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'reason' => $reason,
            'description' => $description,
        ]);
    }

    public function withdraw($amount, $reason = '', $description = null)
    {
        if ($this->wallet_balance < $amount) {
            return false;
        }

        $before = $this->wallet_balance;
        $this->decrement('wallet_balance', $amount);
        $after = $this->wallet_balance;

        return $this->walletTransactions()->create([
            'type' => 'withdraw',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'reason' => $reason,
            'description' => $description,
        ]);
    }
}
