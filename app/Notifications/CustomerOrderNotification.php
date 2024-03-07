<?php

namespace App\Notifications;

use App\Enums\Notification\NotificationType;
use App\Enums\Order\OrderStatus;
use App\Models\Order;
use App\Services\Contract\InvoiceServiceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CustomerOrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected InvoiceServiceContract $invoiceService)
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
        return [
            NotificationType::Mail->value,
            NotificationType::Telegram->value
        ];
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
//            ->line("You can see your order details in invoice clicking on the button below")
//            ->action('Notification Action', url('/'))
            ->line('You can see your order details inside attached file')
            ->attach(
                Storage::disk('public')->path($invoice->filename),
                [
                    'as' => $invoice->filename,
                    'mime' => 'application/pdf',
                ]
            );
    }

    public function toTelegram(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }
}
