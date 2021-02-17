<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Term;
use App\Services\Rule\LegacyImport;
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
        (new \App\Services\LegacyImport\Rule())->handle();

        if (app()->environment() === 'production') {
            /**
             * importing legacy data from mongo dump
             */
            (new \App\Services\LegacyImport\Rule())->handle();
        } else {

            Rule::factory()->count(5)->create([
                'client_account_id' => ClientAccount::where('name', 'Unilever')->first()->id,
            ])->each(function (Rule $rule) {
                $rule->terms()->attach(Term::inRandomOrder()->take(2)->get()->pluck('id')->all());
            });

        }
    }
}
