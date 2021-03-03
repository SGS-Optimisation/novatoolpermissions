<?php


namespace App\Services\Taxonomy\Traits;


use App\Models\ClientAccount;
use App\Services\ClientAccounts\BuildTaxonomyLists;
use App\Services\ClientAccounts\BuildTaxonomyWithUsage;
use Illuminate\Support\Facades\Cache;

trait TaxonomyBuilder
{
    protected function buildTaxonomyLists($client_account)
    {
        if(is_string($client_account)) {
            $client_account = ClientAccount::whereSlug($client_account)->first();
        } elseif(is_int($client_account)) {
            $client_account = ClientAccount::find($client_account);
        }

        return Cache::tags('taxonomy')->remember(
            $client_account->slug.'-rules-data',
            3600,
            function () use ($client_account) {

                $taxonomy_builder = (new BuildTaxonomyLists($client_account))->handle();

                return [
                    'clientAccount' => $client_account,
                    'taxonomyHierarchy' => $taxonomy_builder->taxonomy_hierarchy,
                    'topTaxonomies' => $taxonomy_builder->top_taxonomies,
                ];
            });
    }

    protected function buildTaxonomyWithUsage($client_account)
    {
        if(is_string($client_account)) {
            $client_account = ClientAccount::whereSlug($client_account)->first();
        } elseif(is_int($client_account)) {
            $client_account = ClientAccount::find($client_account);
        }

        return Cache::tags('taxonomy')->remember(
            $client_account->slug.'-taxonomy-usage-data',
            3600,
            function () use ($client_account) {

                $taxonomy_builder = (new BuildTaxonomyWithUsage($client_account))->handle();

                return [
                    'clientAccount' => $client_account,
                    'taxonomyHierarchy' => $taxonomy_builder->taxonomy_hierarchy,
                    'topTaxonomies' => $taxonomy_builder->top_taxonomies,
                ];
            });
    }



}
