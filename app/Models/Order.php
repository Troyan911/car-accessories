<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'status_id',
        'user_id',
        'name',
        'surname',
        'email',
        'phone',
        'address',
        'city',
        'total',
        'created_at',
        'updated_at',
        'vendor_order_id',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('title', 'quantity', 'single_price');
    }

    //HasOne??
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return "$this->name $this->surname";
            }
        );
    }
}
