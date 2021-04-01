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
                foreach ($list as $taxonomy_name => $taxonomy_data) {
                    $config = $vocab_config;

                    if (Arr::has($taxonomy_data, 'config')) {
                        $config = array_merge($taxonomy_data['config'], $vocab_config);
                    }

                    $taxonomy = static::buildTaxonomy($taxonomy_name, $config, $parent);

                    if (Arr::has($taxonomy_data, 'mappings')) {
                        foreach ($taxonomy_data['mappings'] as $mapping) {
                            $taxonomy->mappings()->create($mapping);
                        }
                    }

                    if (Arr::has($taxonomy_data, 'children')) {
                        static::processTaxonomies($taxonomy_data['children'], $vocab_config, $term_config, $taxonomy,
                            $client_account, $rule);
                    }

                    if (Arr::has($taxonomy_data, 'terms')) {
                        foreach ($taxonomy_data['terms'] as $term) {
                            static::buildTerm($term, $taxonomy, $term_config, $rule, $client_account);
                        }
                    }

                    if ($client_account) {
                        $taxonomy->client_accounts()->syncWithoutDetaching($client_account);
                    }

                }
            } else {
                foreach ($list as $taxonomy_name) {
                    $taxonomy = static::buildTaxonomy($taxonomy_name, $vocab_config, $parent);

                    if ($client_account) {
                        $taxonomy->client_accounts()->syncWithoutDetaching($client_account);
                    }
                }
            }
        }
    }

    /**
     * @param $name
     * @param $config
     * @param  Taxonomy|null  $parent
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

        $taxonomy = Taxonomy::where($taxonomy_data)->whereRaw('config = cast(? as json)',
            json_encode($config))->first();

        if (!$taxonomy) {
            $taxonomy_data['config'] = $config;
            $taxonomy = Taxonomy::create($taxonomy_data);
        }

        return $taxonomy;
    }

    public static function createAccountStructureTaxonomy($designation, $client_account, $rule = null)
    {
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

    public static function createJobCategorizationTaxonomy($categorizations, $client_account)
    {
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
