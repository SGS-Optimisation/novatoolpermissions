<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ClientAccount;
use App\Models\Rule;

class RuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_account_id' => ClientAccount::factory(),
            'name' => fake()->words(random_int(3,5), true),
            'content' => fake()->paragraphs(3, true),
            'metadata' => '{}',
            'flagged' => fake()->boolean(10),
        ];
    }
}
