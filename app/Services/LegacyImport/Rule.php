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
            "JobDesignations",
            'CreatedAt',
            'UpdatedAt'
        ])->get()->each(function ($item) {
            $client_account = ClientAccount::whereLegacyId($item->Project)->first();
            $name = strtok(preg_replace('/^\s+\n/', '', strip_tags(html_entity_decode(preg_replace("/&nbsp;/",'',$item->Description)))), "\n");

            $rule = \App\Models\Rule::create([
                'name' => $name ? $name : $item->_id,
                'client_account_id' => $client_account->id,
                'content' => $item->Description,
                'created_at' => $item->CreatedAt->toDateTime(),
                'updated_at' => $item->UpdatedAt->toDateTime(),
            ]);

            collect($item->JobDesignations)->each(function ($designation) use ($client_account, $rule) {
                static::createAccountStructureTaxonomy($designation, $client_account, $rule);
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
                    $client_account,
                    $rule
                );
            }

        });
    }

}
