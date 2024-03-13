<?php

namespace App\Listeners\Admin;

use App\Events\OrderCreated;
use App\Jobs\OrderCreatedNotifyJob;

class OrderCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        logs()->info('run the job'.OrderCreatedNotifyJob::class);
        OrderCreatedNotifyJob::dispatch($event->order);
        //            ->delay(now()->addSecond(20))
    }
}
