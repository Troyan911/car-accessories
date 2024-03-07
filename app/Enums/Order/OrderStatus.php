<?php

namespace App\Enums\Order;

enum OrderStatus: string
{
    case InProcess = 'In process';
    case Paid = 'Paid';
    case Completed = 'Completed';
    case Canceled = 'Canceled';

    public function findByKey(string $key)
    {
        return constant("self: $key");
    }
}
