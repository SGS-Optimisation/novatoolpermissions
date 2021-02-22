<?php


namespace App\Services\Taxonomy\Traits;


use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Taxonomy;
use App\Services\Term\Traits\TermHelper;
use Illuminate\Support\Arr;

trait TaxonomyCreationHelper
{
    use TermHelper;

    /**
     * @param $list
     * @param  bool[]  $vocab_config
     * @param  bool[]  $term_config
     * @param  Taxonomy|null  $parent
     * @param  ClientAccount|null  $client_account
     * @param  Rule|null  $rule
     */
    public static function processTaxonomies(
        $list,
        $vocab_config = ['default' => true],
        $term_config = ['default' => true],
        $parent = null,
        $client_account = null,
        $rule = null
    ) {
        if ($list) {
            //logger('taxonomies: processing list:' . print_r($list, true) );
            if (Arr::isAssoc($list)) {
                foreach ($list as $name => $items) {
                    $taxonomy = static::buildTaxonomy($name, $vocab_config, $parent);

                    if (Arr::has($items, 'children')) {
                        static::processTaxonomies($items['children'], $vocab_config, $term_config, $taxonomy, $client_account, $rule);
                    }

                    if (Arr::has($items, 'terms')) {
                        foreach ($items['terms'] as $term) {
                            static::buildTerm($term, $taxonomy, $term_config, $rule);
                        }
                    }

                    if($client_account){
                        $taxonomy->client_accounts()->syncWithoutDetaching($client_account);
                    }

                }
            } else {
                foreach ($list as $name) {
                    $taxonomy = static::buildTaxonomy($name, $vocab_config, $parent);

                    if($client_account){
                        $taxonomy->client_accounts()->syncWithoutDetaching($client_account);
                    }
                }
            }
        }
    }

    /**
     * @param $name
     * @param $config
     * @param Taxonomy|null $parent
     * @return Taxonomy|\Illuminate\Database\Eloquent\Model
     */
    public static function buildTaxonomy($name, $config, $parent = null)
    {
        $taxonomy_data = [
            'name' => $name
        ];

        if ($parent) {
            $taxonomy_data['parent_id'] = $parent->id;
        }

        $taxonomy = Taxonomy::where($taxonomy_data)->whereRaw('config = cast(? as json)', json_encode($config))->first();

        if(!$taxonomy){
            $taxonomy_data['config'] = $config;
            $taxonomy = Taxonomy::create($taxonomy_data);
        }

        return $taxonomy;
    }

    public static function createAccountStructureTaxonomy($designation, $client_account, $rule = null){
        static::processTaxonomies([
            'Account Structure' => [
                'children' => [
                    $designation['Title'] => [
                        'terms' => $designation['Subjobs']
                    ]
                ]
            ]
        ],
            ['default' => false],
            ['default' => false],
            null,
            $client_account,
            $rule
        );
    }

    public static function createJobCategorizationTaxonomy($categorizations, $client_account){
        static::processTaxonomies([
            'Job Categorizations' => [
                'children' => [
                    $categorizations['Title'] => [
                        'terms' => $categorizations['Subcategories'] ? $categorizations['Subcategories'] : []
                    ]
                ]
            ]
        ],
            ['default' => false],
            ['default' => false],
            null,
            $client_account
        );
    }

}
