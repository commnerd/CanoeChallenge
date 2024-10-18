<?php

namespace Tests\Feature\Listeners;

use App\Events\DuplicateFundWarningEvent;
use App\Listeners\DuplicateFundWarningListener;
use App\Models\{Company, DuplicateFund, Fund};
use Tests\Feature\TestCase;

class DuplicateFundWarningListenerTest extends TestCase
{
    /**
     * Setup necessary for most tests
     */
    public function setUp(): void
    {
        parent::setUp();

        Company::factory()->create();
    }

    /**
     * A test to ensure a duplicate fund makes it into the database.
     */
    public function test_duplicate_fund_created_when_fired(): void
    {
        $fund = Fund::factory()->create();
        $listener = app()->make(DuplicateFundWarningListener::class);
        $event = new DuplicateFundWarningEvent($fund);
        $listener->handle($event);

        $this->assertEquals(1, DuplicateFund::where('fund_id', $fund->id)->count());
    }
}
