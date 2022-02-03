<?php


namespace App\Services\ClientAccounts;


use App\Models\Taxonomy;
use App\Models\Term;

class BuildTaxonomyWithUsage extends BaseClientAccountService
{
    /** @var Taxonomy[] */
    public $top_taxonomies;

    public $taxonomy_hierarchy;

    public function handle()
    {
        $client_account = $this->clientAccount;

        $this->top_taxonomies = $this->clientAccount->taxonomies()
            //->with('taxonomies.terms')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();


        foreach ($this->top_taxonomies as $top_taxonomy) {
            $this->taxonomy_hierarchy[$top_taxonomy->name]['children'] = [];

            foreach ($top_taxonomy->taxonomies()
                         ->whereHas('client_accounts', function ($query) use ($client_account) {
                             return $query->whereIn('id', [$client_account->id]);
                         })->with('mappings')
                         ->get() as $taxonomy) {

                $this->taxonomy_hierarchy[$top_taxonomy->name]['children'][] = static::processTaxonomy($taxonomy);
            }

            $this->taxonomy_hierarchy[$top_taxonomy->name]['unlinked'] = $top_taxonomy->taxonomies()
                ->whereDoesntHave('client_accounts', function ($query) use ($client_account) {
                    return $query->whereIn('id', [$client_account->id]);
                })->pluck('name')->all();
        }

        return $this;
    }

    protected function processTaxonomy(Taxonomy $taxonomy)
    {
        $data = [];
        $data[$taxonomy->name] = [
            'id' => $taxonomy->id,
            'taxonomy' => $taxonomy,
            'terms' => []
        ];

        if ($taxonomy->taxonomies()->count()) {

            $data[$taxonomy->name]['children'] = [];

            foreach ($taxonomy->taxonomies as $child_taxonomy) {

                $data[$taxonomy->name]['children'][$child_taxonomy] = [];

                $data[$taxonomy->name]['children'][$child_taxonomy][] = static::processTaxonomy($child_taxonomy);
            }
        }

        $client_terms = $taxonomy->terms()
            ->whereHas('client_accounts', function ($query) {
                $query->where('id', $this->clientAccount->id);
            })
            ->withCount('rules')
            ->get();

        if (count($client_terms)) {

            /** @var Term $term */
            foreach ($client_terms as $term) {

                $client_rules_count = $term->rules()->with(['terms'])->where('client_account_id', $this->clientAccount->id)
                    ->count();

                $data[$taxonomy->name]['terms'][] = [
                    'id' => $term->id,
                    'name' => $term->name,
                    'globalRulesCount' => (int) $term->rules_count,
                    'clientRulesCount' =>  (int) $client_rules_count,
                    'aliases' => $term->config['aliases'] ?? []
                ];
            }

            $cleanTerms = collect($data[$taxonomy->name]['terms'])->unique()->sort();

            $data[$taxonomy->name]['terms'] = $cleanTerms->values()->all();


        }

        return $data;
    }
}
