<?php

namespace App\Enums;

enum PaymentSystem: string
{
    case PAYPAL = 'paypal';
    case LIQPAY = 'liqpay';
}
