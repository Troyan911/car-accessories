<?php

namespace App\Notifications\Product;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AvailableNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Product $product)
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line("Hi, $notifiable->fullName")
            ->line("Product which you've added to wishlist ")
            ->line("\'{$this->product->title}\'")
            ->line('Has became available for order')
            ->action('Open product', url(route('products.show', $this->product)))
            ->line('Thank you for using our application!');
    }
}
