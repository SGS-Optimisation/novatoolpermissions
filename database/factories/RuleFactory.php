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
            'name' => $this->faker->name,
            'content' => $this->faker->paragraphs(3, true),
            'metadata' => '{}',
            'flagged' => $this->faker->boolean,
        ];
    }
}
