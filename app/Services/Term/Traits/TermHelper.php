<?php


namespace App\Services\Term\Traits;


use App\Models\Rule;
use App\Models\Taxonomy;
use App\Models\Term;

trait TermHelper
{

    /**
     * @param $name
     * @param  Taxonomy  $taxonomy
     * @param  array  $config
     * @param  Rule|null  $rule
     */
    protected static function buildTerm($name, $taxonomy, $config = [], $rule = null)
    {
        $term = Term::whereName($name)
            ->whereTaxonomyId($taxonomy->id)
            ->whereRaw('config = cast(? as json)', json_encode($config))
            ->first();

        if(!$term){
            $term = $taxonomy->terms()->create(['name' => $name, 'config' => $config]);
        }

        if($rule)
        {
            $rule->terms()->attach($term->id);
        }

    }

}
