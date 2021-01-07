<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rule::factory()->count(5)->create([
            'client_account_id' => ClientAccount::query()->inRandomOrder()->first(),
        ])->each(function(Rule $rule){
            $rule->terms()->attach(Term::inRandomOrder()->take(2)->get()->pluck('id')->all());
        });
    }
}
