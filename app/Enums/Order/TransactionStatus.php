<?php

namespace App\Enums\Order;

enum TransactionStatus: string
{
    case Success = 'success';
    case Canceled = 'canceled';
    case Pending = 'pending';
}
