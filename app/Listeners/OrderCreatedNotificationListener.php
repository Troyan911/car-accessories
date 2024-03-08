<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\OrderCreatedNotifyJob;

class OrderCreatedNotificationListener
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
        logs()->info('run the job');
        OrderCreatedNotifyJob::dispatch($event->order);
        //            ->delay(now()->addSecond(20))
    }
}
