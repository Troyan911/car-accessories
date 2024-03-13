<?php

namespace App\Jobs\Products;

use App\Enums\Notification\JobQueue;
use App\Enums\User\SubscriptionType;
use App\Models\Product;
use App\Notifications\Product\AvailableNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class AvailableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product)
    {
        $this->onQueue(JobQueue::Notifications->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logs()->info(__CLASS__.': Quantity update');

        Notification::send(
            $this->product->followers()->wherePivot(SubscriptionType::Available->value, true)->get(),
            app()->make(AvailableNotification::class, ['product' => $this->product])
        );
    }
}
