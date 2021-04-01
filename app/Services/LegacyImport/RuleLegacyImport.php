<?php


namespace App\Services\LegacyImport;


use App\Legacy\Models\Specification;
use App\Models\ClientAccount;
use App\Services\BaseService;
use App\Services\Taxonomy\Traits\TaxonomyCreationHelper;

class RuleLegacyImport extends BaseService
{

    use TaxonomyCreationHelper;

    public $problem_rules = [];

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

            if ($client_account) {

                $name = strtok(preg_replace('/^\s+\n/', '',
                    strip_tags(html_entity_decode(preg_replace("/&nbsp;/", '', $item->Description)))), "\n");

                $content = (new ExtractImages($item->Description))->handle()->updated_content;

                $rule = \App\Models\Rule::create([
                    'client_account_id' => $client_account->id,
                    'name' => $name ? $name : $item->_id,
                    'content' => $content,
                    'created_at' => $item->CreatedAt->toDateTime(),
                    'updated_at' => $item->UpdatedAt->toDateTime(),
                ]);

                collect($item->JobDesignations)->each(function ($designation) use ($client_account, $rule) {
                    static::createAccountStructureTaxonomy($designation, $client_account, $rule);
                });

                if ($item->RuleCategorization) {
                    $taxonomy_name = $item->RuleCategorization;
                    $terms = $item->SubRuleCategorization ? [$item->SubRuleCategorization] : [];

                    static::createJobCategorizationTaxonomy($taxonomy_name, $terms, $client_account, $rule);

                    /*static::processTaxonomies([
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
                    );*/
                }
            } else {
                // if client is not present log it inside
                \Log::debug("No Client Available: " . $item->Project);
            }

        });
    }

}
