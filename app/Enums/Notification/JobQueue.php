<?php

namespace App\Enums\Notification;

enum JobQueue: string
{
    case Default = 'default';
    case Notifications = 'notifications';
    case AdminNotifications = 'AdminNotifications';
    case CustomerNotifications = 'CustomerNotifications';
}
