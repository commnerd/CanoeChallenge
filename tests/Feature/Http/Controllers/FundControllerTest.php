<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\{Company, Fund};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\TestCase;

class FundControllerTest extends TestCase
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
     * A basic index call.
     */
    public function test_index_endpoint(): void
    {
        $response = $this->get(route('api.funds.index'));

        $response->assertStatus(200);
    }

    /**
     * A basic index call with a record
     */
    public function test_index_with_record(): void
    {
        $fund = Fund::factory()->create();

        $response = $this->getJson(route('api.funds.index'));

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $fund->id);
        $response->assertJsonPath('data.0.name', $fund->name);
        $response->assertJsonPath('data.0.year', $fund->year);
        $response->assertJsonPath('data.0.fund_manager_id', $fund->fund_manager_id);
    }

    /**
     * Filter records by name
     */
    public function test_index_filter_by_name(): void
    {
        Company::factory(50)->create()->each(function ($company, $index) {
            if($index % 2 > 0) {
                Fund::factory()->create();
            }
        });

        Fund::inRandomOrder()->limit(7)->get()->each(function($fund) {
            $fund->update(['name' => 'abcdefg '.$fund->name]);
        });

        $response = $this->getJson(route('api.funds.index', ['name' => 'bcdef']));

        $response->assertJsonPath('total', 7);
    }

    /**
     * Filter record by fund_manager_id
     */
    public function test_index_filter_by_fund_manager_id(): void
    {
        Company::factory(50)->create()->each(function ($company, $index) {
            if($index % 2 > 0) {
                Fund::factory()->create();
            }
        });

        $fundManagerId = Fund::select('fund_manager_id')->inRandomOrder()->first()->fund_manager_id;
        $response = $this->getJson(route('api.funds.index', ['fund_manager' => $fundManagerId]));

        $response->assertJsonPath('total', 1);
    }

    /**
     * Filter records by year
     */
    public function test_index_filter_by_year(): void
    {
        Company::factory(50)->create()->each(function ($company, $index) {
            if($index % 2 > 0) {
                Fund::factory()->create();
            }
        });

        Fund::inRandomOrder()->limit(7)->get()->each(function($fund) {
            $fund->update(['start_year' => '1742']);
        });

        $response = $this->getJson(route('api.funds.index', ['year' => '1742']));

        $response->assertJsonPath('total', 7);
    }

    /**
     * A basic index call with a record
     */
    public function test_store_endpoint(): void
    {
        $fund = Fund::factory()->make();

        $response = $this->post(route('api.funds.store'), $fund->toArray());

        $response->assertStatus(201);
        $this->assertEquals(1, Fund::count());
        $response->assertJsonPath('data.id', 1);
        $response->assertJsonPath('data.name', $fund->name);
        $response->assertJsonPath('data.start_year', $fund->start_year);
        $response->assertJsonPath('data.fund_manager_id', $fund->fund_manager_id);
    }

    /**
     * A basic show endpoint call
     */
    public function test_show_endpoint(): void
    {
        $fund = Fund::factory()->create();

        $response = $this->getJson(route('api.funds.show', $fund));

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $fund->id);
        $response->assertJsonPath('data.name', $fund->name);
        $response->assertJsonPath('data.start_year', $fund->start_year);
        $response->assertJsonPath('data.fund_manager_id', $fund->fund_manager_id);
    }

    /**
     * A basic update endpoint call
     */
    public function test_update_endpoint(): void
    {
        $fund = Fund::factory()->create();
        $fundUpdate = Fund::factory()->make([
            'fund_manager_id' => $fund->fund_manager_id,
        ]);

        $response = $this->put(route('api.funds.show', $fund), $fundUpdate->toArray());

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $fund->id);
        $response->assertJsonPath('data.name', $fundUpdate->name);
        $response->assertJsonPath('data.start_year', $fundUpdate->start_year);
        $response->assertJsonPath('data.fund_manager_id', $fundUpdate->fund_manager_id);
    }

    /**
     * A basic destroy endpoint call
     */
    public function test_destroy_endpoint(): void
    {
        $fund = Fund::factory()->create();

        $response = $this->delete(route('api.funds.show', $fund));

        $response->assertStatus(200);
        $this->assertEquals(0, Fund::count());
    }
}
