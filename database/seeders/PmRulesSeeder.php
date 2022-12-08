<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Taxonomy;
use Database\Factories\RuleFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PmRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ca = ClientAccount::whereSlug('unilever')
            ->with('account_structure_child_taxonomies.terms')
            ->first();

        $pm_elements = Taxonomy::whereName('PM Section Elements')->with('terms')->first();
        $acct_structure_taxonomies = $ca->account_structure_child_taxonomies;

        $rules = Rule::factory()
            ->count(10)
            ->for($ca)
            ->create([
                'is_pm' => true,
            ])
            ->each(function ($rule) use ($acct_structure_taxonomies, $pm_elements, $ca) {
                $term_ids = [];

                $pm_term = $pm_elements->terms->random();

                $term_ids[] = $pm_term->id;
                //$rule->terms()->sync($pm_term->id);

                $num_tags = random_int(2, 5);
                for($i = 0; $i < $num_tags; $i++) {
                    $client_terms = $acct_structure_taxonomies->terms()
                        ->whereHas('client_accounts', function ($query) use ($ca) {
                            $query->where('id', $ca->id);
                        })
                        ->withCount('rules')
                        ->get();

                    $term = $client_terms->random();
                    $term_ids[] = $term->id;
                }

                $rule->terms()->sync($term_ids);

                $rule->update(['state' => 'Published']);
            });

        $omnipresent_rules = Rule::factory()
            ->count(10)
            ->for($ca)
            ->create([
                'is_pm' => true,
            ])
            ->each(function ($rule) use ($acct_structure_taxonomies, $pm_elements) {
                $term_ids = [];

                $pm_term = $pm_elements->terms->random();

                $term_ids[] = $pm_term->id;
                //$rule->terms()->sync($pm_term->id);

                $num_tags = random_int(1, 2);
                for($i = 0; $i < $num_tags; $i++) {
                    $term = $acct_structure_taxonomies->random()->terms()->firstOrCreate(['name' => 'ANY']);
                    $term_ids[] = $term->id;
                }

                $rule->terms()->sync($term_ids);

                $rule->update(['state' => 'Published']);
            });
    }
}
