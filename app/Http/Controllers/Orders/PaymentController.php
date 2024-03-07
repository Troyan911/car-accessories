<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;

class PaymentController extends Controller
{
    public function __invoke(Order $order)
    {
        $this->authorize('view', $order);
        $order->loadMissing(['user', 'transaction', 'products']);
        $tax = config('cart.tax') / 100;
        Cart::instance('cart')->destroy();
        \App\Events\OrderCreated::dispatch($order);

        //todo move paypal to separate class
        return view('payments/paypal-thankyou', compact('order', 'tax'));
    }
}
