<?php

namespace Tests\Feature\Http\Controllers;

use App\Events\DuplicateFundWarningEvent;
use App\Listeners\DuplicateFundWarningListener;
use App\Models\{Company, Fund, FundAlias};
use Illuminate\Support\Facades\Event;
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
                Fund::factory()->create([
                    'fund_manager_id' => $company->id
                ]);
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
     * A basic store call
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
     * A store call with aliases
     */
    public function test_store_with_aliases(): void
    {
        $fund = Fund::factory()->make();
        $fund->aliases = [
            ['name' => 'something'],
            ['name' => 'something else'],
        ];

        $response = $this->post(route('api.funds.store'), $fund->toArray());

        $response->assertStatus(201);
        $this->assertEquals(2, FundAlias::count());
    }

    /**
     * A store call with portfolio entries
     */
    public function test_store_with_portfolio(): void
    {
        Company::factory(2)->create();

        $fund = Fund::factory()->make();
        $fund->portfolio = [2,3];

        $response = $this->post(route('api.funds.store'), $fund->toArray());

        $fund = Fund::with('portfolio')->findOrFail(1);
        $response->assertStatus(201);
        $this->assertEquals(2, $fund->portfolio()->count());
    }

    /**
     * A basic test for the duplicate_fund_warning event
     */
    public function test_store_with_matching_name_duplication_warning(): void
    {
        Event::fake([
            DuplicateFundWarningEvent::class,
        ]);
        $fund = Fund::factory()->create();

        $response = $this->post(route('api.funds.store'), $fund->toArray());

        $response->assertStatus(201);
        $this->assertEquals(2, Fund::count());
        Event::assertDispatched(DuplicateFundWarningEvent::class);

    }

    /**
     * A basic test for the duplicate_fund_warning listener
     */
    public function test_store_with_matching_name_duplication_listener(): void
    {
        Event::fake([
            DuplicateFundWarningEvent::class,
        ]);
        $fund = Fund::factory()->create();

        $response = $this->post(route('api.funds.store'), $fund->toArray());

        $response->assertStatus(201);
        $this->assertEquals(2, Fund::count());
        Event::assertListening(DuplicateFundWarningEvent::class, DuplicateFundWarningListener::class);

    }

    /**
     * A test for the duplicate_fund_warning event on duplicate alias
     */
    public function test_store_with_matching_alias_duplication_warning(): void
    {
        Event::fake([
            DuplicateFundWarningEvent::class,
        ]);
        $fund = Fund::factory()->create();
        $alias = FundAlias::factory()->create([
            'name' => fake()->name,
            'fund_id' => $fund->id,
        ]);

        $newFund = Fund::factory()->make([
            'name' => $alias->name,
            'fund_manager_id' => $fund->fund_manager_id,
        ]);

        $response = $this->post(route('api.funds.store'), $newFund->toArray());

        $response->assertStatus(201);
        $this->assertEquals(2, Fund::count());
        Event::assertDispatched(DuplicateFundWarningEvent::class);

    }

    /**
     * A basic show endpoint call
     */
    public function test_show_endpoint(): void
    {
        
        $fund = Fund::factory()->create();
        $portfolio = [];
        $fund->aliases()->create(['name' => 'something']);
        $fund->aliases()->create(['name' => 'another thing']);
        Company::factory(2)->create()->each(function($company) use ($fund, &$portfolio) {
            $fund->portfolio()->save($company);
            $portfolio[] = $company->name;
        });
        
        $response = $this->getJson(route('api.funds.show', $fund));

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $fund->id);
        $response->assertJsonPath('data.name', $fund->name);
        $response->assertJsonPath('data.start_year', $fund->start_year);
        $response->assertJsonPath('data.fund_manager_id', $fund->fund_manager_id);
        $response->assertJsonPath('data.aliases.0.name', 'something');
        $response->assertJsonPath('data.aliases.1.name', 'another thing');
        $response->assertJsonPath('data.portfolio.0.name', $portfolio[0]);
        $response->assertJsonPath('data.portfolio.1.name', $portfolio[1]);
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

        $response = $this->put(route('api.funds.update', $fund), $fundUpdate->toArray());

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $fund->id);
        $response->assertJsonPath('data.name', $fundUpdate->name);
        $response->assertJsonPath('data.start_year', $fundUpdate->start_year);
        $response->assertJsonPath('data.fund_manager_id', $fundUpdate->fund_manager_id);
    }

    /**
     * An update with aliases
     */
    public function test_update_endpoint_with_aliases(): void
    {
        $fund = Fund::factory()->create();
        $fundUpdate = Fund::factory()->make([
            'fund_manager_id' => $fund->fund_manager_id,
        ]);
        $fundUpdate->aliases = [
            ['name' => 'biz'],
            ['name' => 'baz'],
        ];

        $response = $this->put(route('api.funds.update', $fund), $fundUpdate->toArray());

        $response->assertStatus(200);
        $this->assertEquals(2, FundAlias::count());
    }

    /**
     * An update with an alias reduction
     */
    public function test_update_endpoint_with_alias_reduction(): void
    {
        $fund = Fund::factory()->create();
        FundAlias::factory(2)->create([
            'fund_id' => $fund->id
        ]);

        $fund = Fund::with('aliases')->findOrFail($fund->id);
        $fundPayload = $fund->toArray();
        unset($fundPayload['aliases'][0]);

        $response = $this->put(route('api.funds.update', $fund), $fundPayload);

        $response->assertStatus(200);
        $this->assertEquals(1, FundAlias::count());
    }

    /**
     * An update call with portfolio entries
     */
    public function test_update_endpoint_with_portfolio_entries(): void
    {
        Company::factory(3)->create();
        $fund = Fund::factory()->create();
        $fund->portfolio()->sync([2,3,4]);

        $fund->load('portfolio');

        $fundPayload = $fund->toArray();
        $fundPayload['portfolio'] = [2, 4];

        $response = $this->put(route('api.funds.update', $fund), $fundPayload);

        $fund->load('portfolio');
        $response->assertStatus(200);
        $this->assertEquals(2, $fund->portfolio()->count());
    }

    /**
     * An update call with portfolio entry reduction
     */
    public function test_update_endpoint_with_portfolio_entry_reduction(): void
    {
        Company::factory(3)->create();
        $fund = Fund::factory()->create();
        $fundUpdate = Fund::factory()->make([
            'fund_manager_id' => $fund->fund_manager_id,
        ]);
        $fundUpdate->portfolio = [2,3,4];

        $response = $this->put(route('api.funds.update', $fund), $fundUpdate->toArray());

        $response->assertStatus(200);
        $this->assertEquals(3, $fund->portfolio()->count());
    }

    /**
     * A basic destroy endpoint call
     */
    public function test_destroy_endpoint(): void
    {
        $fund = Fund::factory()->create();

        $response = $this->delete(route('api.funds.destroy', $fund));

        $response->assertStatus(200);
        $this->assertEquals(0, Fund::count());
    }
}
