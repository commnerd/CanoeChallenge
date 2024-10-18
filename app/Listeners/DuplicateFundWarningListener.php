<?php

namespace App\Listeners;

use App\Events\DuplicateFundWarningEvent;
use App\Models\Fund;

class DuplicateFundWarningListener
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
    public function handle(DuplicateFundWarningEvent $event): void
    {
        dd($event);
    }
}
