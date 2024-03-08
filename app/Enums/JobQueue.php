<?php

namespace App\Enums;

enum JobQueue: string
{
    case Default = 'default';
    case Notifications = 'notifications';
    case AdminNotifications = 'AdminNotifications';
    case CustomerNotifications = 'CustomerNotifications';
}
