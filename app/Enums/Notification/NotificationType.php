<?php

namespace App\Enums\Notification;

enum NotificationType: string
{
    case Mail = 'mail';
    case Telegram = 'telegram';
    case Sms = 'sms';
    case Viber = 'viber';

    public function getValue(NotificationType $type): string
    {
        return $type->value;
    }
}
