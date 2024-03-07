<?php

namespace App\Models;

use App\Enums\Order\OrderStatus as Status;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public $timestamps = false;

    protected $casts = [
        'name' => Status::class,
    ];

    public function scopeInProgress(Builder $query): Builder
    {
        return $this->statusQuery($query, Status::InProcess);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $this->statusQuery($query, Status::Paid);
    }

    public function scopeCanceled(Builder $query): Builder
    {
        return $this->statusQuery($query, Status::Canceled);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $this->statusQuery($query, Status::Completed);
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    protected function statusQuery(Builder $query, \App\Enums\Order\OrderStatus $status): Builder
    {
        return $query->where('name', $status->value);
    }
}
