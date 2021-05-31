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
    protected static function buildTerm($name, $taxonomy, $config = [], $rule = null, $client_account = null)
    {
        $name = trim($name);
        if (empty($name)) {
            return;
        }

        $term = Term::where(function ($query) use ($name) {
            return $query->whereName($name)->orWhereJsonContains('config->aliases', $name);
        })->whereTaxonomyId($taxonomy->id)->first();

        if (!isset($term)) {
            $term = $taxonomy->terms()->create(['name' => $name, 'config' => $config]);
        }

        if ($client_account) {
            $term->client_accounts()->syncWithoutDetaching($client_account);
        }

        if ($rule) {
            $rule->terms()->attach($term->id);
        }

    }

}
