<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Notifications\NewTransaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTransactionCreatedNotification implements ShouldQueue
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
    public function handle(TransactionCreated $event): void
    {
        $event->transaction->author->notify(new NewTransaction($event->transaction));
    }
}
