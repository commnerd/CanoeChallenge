<?php

namespace App\Listeners;

use App\Events\DuplicateFundWarningEvent;
use App\Models\DuplicateFund;

class DuplicateFundWarningListener
{
    /**
     * Handle the event.
     */
    public function handle(DuplicateFundWarningEvent $event): void
    {
        DuplicateFund::create(['fund_id' => $event->fund->id]);
    }
}
