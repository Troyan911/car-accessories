<?php

namespace App\Enums\User;

enum Roles: string
{
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case CUSTOMER = 'customer';

}
