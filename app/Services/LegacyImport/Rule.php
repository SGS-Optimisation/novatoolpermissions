<?php


namespace App\Services\LegacyImport;


use App\Legacy\Models\Specification;
use App\Models\ClientAccount;
use App\Services\BaseService;
use App\Services\Taxonomy\Traits\TaxonomyHelper;

class Rule extends BaseService
{

    use TaxonomyHelper;

    public function handle()
    {
        Specification::select([
            "Project",
            "RuleCategorization",
            "SubRuleCategorization",
            "Description",
            "JobDesignations"
        ])->get()->each(function ($item) {
            $client_account = ClientAccount::whereLegacyId($item->Project)->first();

            \App\Models\Rule::firstOrCreate([
                'name' => strip_tags(substr($item->Description, 0, strpos($item->Description, "<br />" ) )),
                'client_account_id' => $client_account->id,
                'content' => $item->Description
            ]);

            collect($item->JobDesignations)->each(function ($designation) use ($client_account) {
                static::createAccountStructureTaxonomy($designation, $client_account);
            });

            if ($item->RuleCategorization) {
                static::processTaxonomies([
                        'Job Categorizations' => [
                            'children' => [
                                $item->RuleCategorization => [
                                    'terms' => $item->SubRuleCategorization ? [$item->SubRuleCategorization] : []
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

        });
    }

}
