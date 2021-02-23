<?php


namespace App\Services\ClientAccounts;


use App\Models\Taxonomy;

class BuildTaxonomyLists extends BaseClientAccountService
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
            ->get();


        foreach ($this->top_taxonomies as $top_taxonomy) {
            $this->taxonomy_hierarchy[$top_taxonomy->name]['children'] = [];

            foreach ($top_taxonomy->taxonomies()->whereHas('client_accounts', function ($query) use ($client_account) {
                return $query->whereIn('id', [$client_account->id]);
            })->get() as $taxonomy) {

                $this->taxonomy_hierarchy[$top_taxonomy->name]['children'][] = static::processTaxonomy($taxonomy);
            }
        }

        return $this;
    }

    protected function processTaxonomy(Taxonomy $taxonomy)
    {
        $data = [];

        if ($taxonomy->taxonomies()->count()) {

            $data[$taxonomy->name]['children'] = [];

            foreach ($taxonomy->taxonomies as $child_taxonomy) {

                $data[$taxonomy->name]['children'][$child_taxonomy] = [];

                $data[$taxonomy->name]['children'][$child_taxonomy][] = static::processTaxonomy($child_taxonomy);
            }
        }
        if ($taxonomy->terms()->count()) {
            $data[$taxonomy->name]['terms'] = [];

            foreach ($taxonomy->terms as $term) {
                $data[$taxonomy->name]['terms'][] = $term->name;
            }
        }

        return $data;
    }

}
