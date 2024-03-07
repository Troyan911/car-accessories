<?php

namespace App\Notifications;

use App\Enums\Notification\NotificationType;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class AdminCreteOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {

    }

    public function via(object $notifiable): array
    {
//        logs()->info($notifiable);
        return $notifiable->telegram_id
            ? [NotificationType::Telegram->value]
            : [NotificationType::Mail->value];
    }

    public function toMail(object $notifiable): MailMessage
    {
        logs()->info(__METHOD__);
        return (new MailMessage)
            ->greeting("Hello $notifiable->name $notifiable->surname")
            ->line('There is a new order on the web site');
    }

    public function toTelegram(object $notifiable)
    {
        logs()->info(__METHOD__);
        $url = route('admin.dashboard');

        return TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content('Hello there!')
            ->line('There is a new order on the web site')
            ->line('Check it in admin panel')
            ->button('Go to dashboard', $url);
    }
}
