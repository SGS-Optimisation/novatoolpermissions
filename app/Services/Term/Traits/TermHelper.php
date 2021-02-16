<?php


namespace App\Services\Term\Traits;


use App\Models\Taxonomy;
use App\Models\Term;

trait TermHelper
{

    /**
     * @param $name
     * @param Taxonomy $taxonomy
     */
    protected static function buildTerm($name, $taxonomy, $config = [])
    {
        if(!Term::whereName($name)->whereTaxonomyId($taxonomy->id)->whereRaw('config = cast(? as json)', json_encode($config))->first()){
            $taxonomy->terms()->create(['name' => $name, 'config' => $config]);
        }
    }

}
