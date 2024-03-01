<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Gloudemans\Shoppingcart\Facades\Cart;

class PaypalController extends Controller
{
    public function __invoke(Order $order)
    {
        $order->loadMissing(['user', 'transaction', 'products']);
        $tax = config('cart.tax') / 100;
        Cart::instance('cart')->destroy();

        return view('payments/paypal-thankyou', compact('order', 'tax'));
    }
}
