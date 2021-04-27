<?php


namespace App\Services\Rule;


use App\Models\ClientAccount;
use App\Models\Job;
use App\Models\Term;
use App\Services\MySgs\Mapping\Mapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RuleFilter
{

    public static function findClient($job)
    {
        $customer_name = $job->metadata->basicDetails->retailer->customerName;

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        switch ($driver) {
            case 'sqlsrv':
                $search = 'LOWER("alias") LIKE ?';
                break;
            case 'mysql':
            default:
                $search = 'LOWER(alias) LIKE ?';
        }
        $searchData = ['%'.Str::lower($customer_name).'%'];

        $client = ClientAccount::where('name', 'LIKE', '%'.$customer_name.'%')
            ->orWhereRaw($search, $searchData)
            ->first();

        $job_metadata = $job->metadata;

        if ($client) {
            $job_metadata->client = $client->only(['id', 'name', 'slug', 'image']);
            $job_metadata->client_found = true;
        } else {
            $job_metadata->client_found = false;
            $job_metadata->client = ['name' => $customer_name];
        }

        $job->metadata = $job_metadata;
        $job->save();
    }

    /**
     * @param  Job  $job
     * @return array
     */
    public static function handle(Job $job): array
    {
        $memoizeMapper = memoize(
            function ($job, $mapping) {
                $mapper = new Mapper($job, $mapping);
                return $mapper->run();
            }
        );

        $clientRules = [];

        if (!$job->metadata->client_found) {
            static::findClient($job);
        }

        if ($job->metadata->client_found) {

            $client = ClientAccount::find($job->metadata->client->id);
            logger('loaded client '.$client->name);

            $metadata = $job->metadata;

            $job_taxonomy_terms_matches = [];
            $job_taxonomy_terms = [];

            foreach ($client->rules()->isPublished()->get() as $rule) {
                $matched = true;
                $matchedTaxonomies = [];

                /** @var Term $term */
                foreach ($rule->terms as $term) {
                    /*
                     * Initialize array key, there might be many terms for the same taxonomy
                     */
                    if (!Arr::exists($matchedTaxonomies, $term->taxonomy->name)) {
                        $matchedTaxonomies[$term->taxonomy->name] = false;
                    }

                    /*
                     * If a rule applies to any/all jobs, it should be displayed, skip further checks
                     */
                    if (Str::lower($term->name) === 'any' || Str::lower($term->name) === 'all') {
                        $matchedTaxonomies[$term->taxonomy->name] = true;
                        continue;
                    }

                    /*
                     * Otherwise, check if the field matches
                     */
                    if ($term->taxonomy->mapping) {
                        if (!Arr::exists($job_taxonomy_terms_matches, $term->taxonomy->name)) {
                            $job_taxonomy_terms_matches[$term->taxonomy->name] = [];
                            $job_taxonomy_terms[$term->taxonomy->name] = [];
                        }

                        /**
                         * retrieve value from mysgs response with help of taxonomy
                         */
                        list($mysgsValue, $raw) = $memoizeMapper($job, $term->taxonomy->mapping);

                        $termValue = Str::lower($term->name);
                        $job_taxonomy_terms[$term->taxonomy->name] = $raw;

                        /**
                         * compare retrieved value with this term
                         */
                        if (!(Str::contains($termValue, strtolower($mysgsValue))
                            || Str::contains(strtolower($mysgsValue), $termValue))
                        ) {
                            $matched = false;
                        } else {
                            $matchedTaxonomies[$term->taxonomy->name] = true;

                            if (!in_array($term->name, $job_taxonomy_terms_matches[$term->taxonomy->name])) {
                                $job_taxonomy_terms_matches[$term->taxonomy->name][] = $term->name;
                            }
                        }
                    }
                }

                $taxonomyMatch = true;
                foreach ($matchedTaxonomies as $taxonomy => $state) {
                    $taxonomyMatch &= $state;
                }

                if ($matched || $taxonomyMatch) {
                    $clientRules[] = $rule;
                }
            }

            $metadata->job_taxonomy = $job_taxonomy_terms;
            $metadata->matched_taxonomy = $job_taxonomy_terms_matches;

            $job->metadata = $metadata;
            $job->save();
        }

        return $clientRules;
    }

}
