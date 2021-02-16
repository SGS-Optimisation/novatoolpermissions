<?php


namespace App\Services\Rule;


use App\Legacy\Models\Specification;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Services\Taxonomy\Traits\TaxonomyHelper;

class LegacyImport
{

    use TaxonomyHelper;

    public static function handle()
    {
        Specification::select([
            "Project",
            "RuleCategorization",
            "SubRuleCategorization",
            "Description",
            "JobDesignations"
        ])->get()->each(function ($item) {
            $client_account = ClientAccount::whereLegacyId($item->Project)->first();


            Rule::firstOrCreate([
                'client_account_id' => $client_account->id,
                'content' => $item->Description
            ]);

            collect($item->JobDesignations)->each(function ($designation) use ($client_account) {

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

            if ($item->RuleCategorization) {
                static::processTaxonomies([
                        'Job Categorizations' => [
                            'children' => [
                                $item->RuleCategorization => [
                                    'terms' => $item->SubRuleCategorization ? [ $item->SubRuleCategorization ] : []
                                ]
                            ]
                        ]
                    ],
                    $vocab_config = ['default' => true],
                    $term_config = ['default' => true],
                    $parent = null,
                    $client_account
                );
            }

        });
    }

}
