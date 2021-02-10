<?php


namespace App\Services\Taxonomy\Traits;


use App\Models\Taxonomy;
use Illuminate\Support\Arr;

trait TaxonomyHelper
{

    protected static function processTaxonomies(
        $list,
        $vocab_config = ['default' => true],
        $term_config = ['default' => true],
        $parent = null
    ) {
        if ($list) {
            if (Arr::isAssoc($list)) {
                foreach ($list as $name => $items) {
                    $taxonomy = static::buildTaxonomy($name, $vocab_config, $parent);

                    if (Arr::has($items, 'children')) {
                        static::processTaxonomies($items['children'], $vocab_config, $term_config, $taxonomy);
                    }

                    if (Arr::has($items, 'terms')) {
                        foreach ($items['terms'] as $term) {
                            static::buildTerm($term, $taxonomy, $term_config);
                        }
                    }

                }
            } else {
                foreach ($list as $name) {
                    static::buildTaxonomy($name, $vocab_config, $parent);
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
    protected static function buildTaxonomy($name, $config, $parent = null)
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

    /**
     * @param $name
     * @param Taxonomy $taxonomy
     */
    protected static function buildTerm($name, $taxonomy, $config = [])
    {
        $taxonomy->terms()->create(['name' => $name, 'config' => $config]);
    }

}
