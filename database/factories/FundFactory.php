<?php

namespace Database\Factories;

use App\Models\{Company, Fund};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fund>
 */
class FundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'start_year' => rand(1800, Carbon::now()->year),
            'fund_manager_id' => Company::whereNotIn('id', Fund::select('fund_manager_id')->get()->toArray())->inRandomOrder()->first()->id ?? 1,
        ];
    }
}
