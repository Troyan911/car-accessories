<?php

namespace App\Enums\Account;

enum SubscriptionType: string
{
    case Available = 'available';
    case Price = 'price';

    public static function getValues()
    {
        return array_column(SubscriptionType::cases(), 'value');
    }
}
