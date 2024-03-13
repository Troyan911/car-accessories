<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;

class InvoiceController extends Controller
{
    public function __invoke(Order $order)
    {
        $this->authorize('view', $order);

        return app(\App\Services\Contract\InvoiceServiceContract::class)->generate($order)->stream();
    }
}
