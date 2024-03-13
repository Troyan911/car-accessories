<?php

namespace App\Jobs;

use App\Enums\Notification\JobQueue;
use App\Enums\User\Roles;
use App\Models\Order;
use App\Models\User;
use App\Notifications\AdminCreteOrderNotification;
use App\Notifications\CustomerOrderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class OrderCreatedNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        $this->onQueue(JobQueue::Notifications->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logs()->info(__CLASS__.': Notify customer');
        $this->order->notify(app()->make(CustomerOrderNotification::class));
        logs()->info(__CLASS__.': Notify admins');
        Notification::send(
            User::role(Roles::ADMIN->value)->get(),
            app()->make(AdminCreteOrderNotification::class, ['order' => $this->order])
        );
    }
}
