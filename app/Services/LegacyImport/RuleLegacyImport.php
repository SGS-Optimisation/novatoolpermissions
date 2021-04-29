<?php


namespace App\Services\LegacyImport;


use App\Exports\ImportedRulesErrors;
use App\Legacy\Models\Projet;
use App\Legacy\Models\Specification;
use App\Models\ClientAccount;
use App\Services\BaseService;
use App\Services\Taxonomy\Traits\TaxonomyCreationHelper;
use Maatwebsite\Excel\Facades\Excel;

class RuleLegacyImport extends BaseService
{
    use TaxonomyCreationHelper;

    public $client_name;

    public $imported_rules = [];
    public $problem_rules = [];
    public $num_problems = 0;
    public $problem_file_name = "";

    /**
     * ClientAccountLegacyImport constructor.
     * @param $client_name
     */
    public function __construct($client_name = null)
    {
        $this->client_name = $client_name;
    }


    public function handle()
    {
        $this->processRules();
        $this->exportProblems();

        return $this;
    }

    public function processRules()
    {
        $rules = Specification::select([
            "Project",
            "RuleCategorization",
            "SubRuleCategorization",
            "Description",
            "JobDesignations",
            'CreatedAt',
            'UpdatedAt'
        ])
            ->when($this->client_name, function ($query) {
                $projects = Projet::whereIn(
                    'Name',
                    array_map('trim', explode(',', $this->client_name))
                )->get()->pluck('_id');

                return $query->whereIn('Project', $projects);
            })
            ->get();

        logger('found '.count($rules).' matching');

        $rules->each(function ($item) {
            $client_account = ClientAccount::whereLegacyId($item->Project)->first();

            if ($client_account) {

                if (!isset($this->problem_rules[$client_account->name])) {
                    $this->problem_rules[$client_account->name] = [];
                }

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

                $this->imported_rules[] = $rule;

                $account_structure_success = true;

                collect($item->JobDesignations)->each(function ($designation)
                use ($client_account, $rule, &$account_structure_success) {

                    $account_structure_success &= static::createAccountStructureTaxonomy(
                        $designation, $client_account, $rule
                    );
                });

                $job_categorization_success = true;

                if ($item->RuleCategorization) {
                    $taxonomy_name = $item->RuleCategorization;
                    $terms = $item->SubRuleCategorization ? [$item->SubRuleCategorization] : [];

                    $job_categorization_success = static::createJobCategorizationTaxonomy($taxonomy_name, $terms,
                        $client_account, $rule);

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

                if (!$account_structure_success || !$job_categorization_success) {
                    logger('some issues encountered processing rule');
                    $this->problem_rules[$client_account->name][] = $rule;
                    $this->num_problems++;
                }
            } else {
                // if client is not present log it inside
                \Log::debug("No Client Available: ".$item->Project);
            }
        });

    }

    public function exportProblems()
    {
        $exporter = new ImportedRulesErrors($this->problem_rules);

        $this->problem_file_name = date('Y-m-d-his').'-import_rules_problems.xlsx';
        if ($this->client_name) {
            $this->problem_file_name = $this->client_name.'-'.$this->problem_file_name;
        }

        Excel::store($exporter, $this->problem_file_name, 'public');
    }

}
