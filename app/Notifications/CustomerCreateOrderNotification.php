<?php

namespace App\Notifications;

use App\Enums\Notification\NotificationType;
use App\Enums\Order\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\Contract\InvoiceServiceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use NotificationChannels\Telegram\TelegramMessage;

class CustomerCreateOrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, protected InvoiceServiceContract $invoiceService)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
//        logs()->info($notifiable);
        return $this->user->telegram_id
            ? [NotificationType::Telegram->value]
            : [NotificationType::Mail->value];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Order $order): MailMessage
    {
        logs()->info(self::class);
        $invoice = $this->invoiceService->generate($order);

        return (new MailMessage)
            ->greeting("Hello $order->name $order->surname")
            ->line('Your order was created')
            ->lineIf(
                $order->status->name === OrderStatus::Paid,
                'And successfully paid'
            )
            ->line('You can see your order details inside attached file')
            ->attach(
                Storage::disk('public')->path($invoice->filename),
                [
                    'as' => $invoice->filename,
                    'mime' => 'application/pdf',
                ]
            );
    }

    public function toTelegram(object $notifiable)
    {
        logs()->info(__METHOD__);
        //todo add order page
//        $url = route('account.orders');

        return TelegramMessage::create()
            ->to($this->user->telegram_id)
//            ->content("Hello $order->name $order->surname")
            ->line("You've made new order")
            ->line('Check it in order list');
//            ->button('Go to order', $url);
    }
}
