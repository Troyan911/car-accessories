<?php

namespace App\Enums\Order;

enum PaymentSystem: string
{
    case PAYPAL = 'paypal';
    case LIQPAY = 'liqpay';
}
