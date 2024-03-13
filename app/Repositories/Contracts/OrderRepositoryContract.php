<?php

namespace App\Repositories\Contracts;

use App\Enums\Order\PaymentSystem;
use App\Enums\Order\TransactionStatus;
use App\Models\Order;

interface OrderRepositoryContract
{
    public function create(array $data): Order|bool;

    public function setTransaction(string $vendorOrderId, PaymentSystem $system, TransactionStatus $status): Order;
}
