<?php

namespace App\Listeners;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Auth\Events\Login;

class UserLoginListener
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        Cart::instance('cart')->restore($event->user->id.'_cart');
    }
}
