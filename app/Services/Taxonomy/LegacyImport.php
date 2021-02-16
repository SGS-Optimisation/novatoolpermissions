<?php


namespace App\Services\Taxonomy;


use App\Legacy\Models\Projet;
use App\Models\ClientAccount;
use App\Services\Taxonomy\Traits\TaxonomyHelper;

class LegacyImport
{
    use TaxonomyHelper;

    public static function handle()
    {

        Projet::select(['Designations', 'Categorizations'])->get()->each(function ($projet) {

            $client_account = ClientAccount::whereLegacyId($projet->_id)->first();

            collect($projet->Designations)->each(function ($designation) use ($client_account) {

                static::processTaxonomies([
                        'Account Structure' => [
                            'children' => [
                                $designation['Title'] => [
                                    'terms' => $designation['Subjobs']
                                ]
                            ]
                        ]
                    ],
                    $vocab_config = ['default' => true],
                    $term_config = ['default' => true],
                    $parent = null,
                    $client_account
                );

            });

            collect($projet->Categorizations)->each(function ($categorizations) use ($client_account) {

                static::processTaxonomies([
                        'Job Categorizations' => [
                            'children' => [
                                $categorizations['Title'] => [
                                    'terms' => $categorizations['Subcategories'] ? $categorizations['Subcategories'] : []
                                ]
                            ]
                        ]
                    ],
                    $vocab_config = ['default' => true],
                    $term_config = ['default' => true],
                    $parent = null,
                    $client_account
                );

            });
        });

    }

}
