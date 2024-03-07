<?php

namespace App\Models;

use App\Enums\Order\PaymentSystem;
use App\Enums\Order\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'payment_system', 'status'];

    protected $casts = [
        'payment_system' => PaymentSystem::class,
        'status' => TransactionStatus::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
