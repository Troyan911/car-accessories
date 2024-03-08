<?php

namespace App\Jobs\Products;

use App\Enums\Account\SubscriptionType;
use App\Enums\JobQueue;
use App\Models\Product;
use App\Notifications\Product\PriceDownNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class PriceDownJob implements ShouldQueue
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
        logs()->info(__CLASS__.': Price update');

        $this->product->followers()->wherePivot(SubscriptionType::Price->value, true)->chunk(3, function (Collection $users){
            sleep(10);
            Notification::send(
                $users,
                app()->make(PriceDownNotification::class, ['product' => $this->product])
            );
        });
    }
}
