<?php


namespace App\Services\Taxonomy;


use App\Legacy\Models\Projet;
use App\Services\Taxonomy\Traits\TaxonomyHelper;

class LegacyImport
{
    use TaxonomyHelper;

    public static function handle()
    {

        Projet::select(['Designations', 'Categorizations'])->get()->each(function ($projet) {
            collect($projet->Designations)->each(function ($designation) {

                static::processTaxonomies([
                    'Account Structure' => [
                        'children' => [
                            $designation['Title'] => [
                                'children' => $designation['Subjobs']
                            ]
                        ]
                    ]
                ]);

            });

            collect($projet->Categorizations)->each(function ($categorizations) {

                static::processTaxonomies([
                    'Job Categorizations' => [
                        'children' => [
                            $categorizations['Title'] => [
                                'children' => $categorizations['Subcategories']
                            ]
                        ]
                    ]
                ]);

            });
        });

    }

}
