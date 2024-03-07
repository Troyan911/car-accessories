<?php

namespace App\Enums\Notification;

enum NotificationType: string
{
    case Telegram = 'telegram';

    case Mail = 'mail';
    case Sms = 'sms';

    public function getValue(NotificationType $type): string
    {
        return $type->value;
    }
}
