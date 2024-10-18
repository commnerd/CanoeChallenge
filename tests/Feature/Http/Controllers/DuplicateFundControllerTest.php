<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\{Company, DuplicateFund, Fund};
use Tests\Feature\TestCase;

class DuplicateFundControllerTest extends TestCase
{
    /**
     * A basic call to index page.
     */
    public function test_index(): void
    {
        $response = $this->get(route('api.duplicate_funds.index'));

        $response->assertStatus(200);
    }

    /**
     * Test return of array of duplicates
     */
    public function test_index_array_of_funds(): void
    {
        Company::factory(5)->create()->each(function($company) {
            $fund = Fund::factory()->create([
                'fund_manager_id' => $company->id,
            ]);
            DuplicateFund::create(['fund_id' => $fund->id]);
        });

        $response = $this->get(route('api.duplicate_funds.index'));

        $fund = Fund::firstOrFail();

        $response->assertJsonPath('data.0.id', $fund->id);
        $response->assertJsonPath('data.0.name', $fund->name);
        $response->assertJsonPath('data.0.year', $fund->year);
        $response->assertJsonPath('data.0.fund_manager_id', $fund->fund_manager_id);

    }
}
