<?php


namespace App\Services\Jobs;


use App\Models\ClientAccount;
use App\Models\Job;
use App\Models\Rule;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Operations\Jobs\MatchClientAccountOperation;
use App\Services\MySgs\Api\EloquentHelpers\JobFieldsMapper;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RuleFilter
{

    const FILTER_MODE_PROD = "prod";
    const FILTER_MODE_PM = "pm";

    protected static function noClientAccount()
    {
        logger('no matching client account');
        return [];
    }

    /**
     * @param  Job  $job
     * @return array
     */
    public static function handle(
        Job $job,
        string $mode = RuleFilter::FILTER_MODE_PROD, //
        int $client_id = null
    ): array {

        $cache_key = 'rules-job-'.$job->job_number;
        $cached_rules = cache($cache_key);

        if (!$job->metadata->client_found && !$client_id) {
            logger('no client account associated job, searching');
            (new MatchClientAccountOperation($job))->handle();
        }

        if (!$client_id && (!isset($job->metadata->client) || !isset($job->metadata->client->id))) {
            return static::noClientAccount();
        }

        $client_id = $client_id ?? $job->metadata->client->id;
        $client = ClientAccount::find($client_id);

        if (!$client) {
            return static::noClientAccount();
        }

        logger("checking job $job->job_number with client account $client->name");

        if ($cached_rules === null || !count($cached_rules)) {
            $cached_rules = \Cache::remember(
                $cache_key,
                Carbon::now()->addMinutes(nova_get_setting('job_rules_cache_duration')),
                function () use ($job, $client, $mode) {
                    $start = microtime(true);

                    $memoizeMapper = memoize(
                        function ($mapping) use (&$job) {
                            $mapper = new JobFieldsMapper($job, $mapping);
                            return $mapper->run();
                        }
                    );

                    $clientRules = [];

                    logger('loaded client '.$client->name);

                    $metadata = $job->metadata;

                    $job_taxonomy_terms_matches = [];

                    /*
                     * Data for display to users
                     */
                    $job_taxonomy_terms = [];

                    /**
                     * More complex data for debugging
                     */
                    $job_taxonomy_terms_extra = [];

                    /*
                     *  Match rules against job's metadata
                     */
                    /** @var Rule $rule */
                    foreach ($client->{$mode.'Rules'}()
                                 ->with([
                                     'accountStructureTerms.taxonomy.mappings',
                                     'jobCategorizationsTerms',
                                     'attachments'
                                 ])
                                 ->isPublished()->get() as $rule) {
                        $matched = true;
                        $matchedTaxonomies = [];

                        /** @var Term $term */
                        foreach ($rule->accountStructureTerms as $term) {
                            $mappings = $term->taxonomy->mappings;
                            $taxonomy_name = $term->taxonomy->name;

                            /*
                             * Initialize array key, there might be many terms for the same taxonomy
                             */
                            if (!Arr::exists($matchedTaxonomies, $taxonomy_name)) {
                                $matchedTaxonomies[$taxonomy_name] = false;
                            }

                            /*
                             * If a rule's taxo term applies to any/all jobs, it should be displayed, skip further checks
                             */
                            if (Str::lower($term->name) === 'any' || Str::lower($term->name) === 'all') {
                                $matchedTaxonomies[$taxonomy_name] = true;
                                continue;
                            }

                            /*
                             * Otherwise, check if the field matches
                             */
                            if (count($mappings) > 0) {

                                if (!Arr::exists($job_taxonomy_terms_matches, $taxonomy_name)) {
                                    $job_taxonomy_terms_matches[$taxonomy_name] = [];
                                    $job_taxonomy_terms[$taxonomy_name] = [];
                                }

                                foreach ($mappings as $mapping) {
                                    //logger('rule comparison using mapping '.$mapping->id);
                                    /**
                                     * retrieve value from mysgs response with help of taxonomy
                                     */
                                    list($mysgsValue, $raw) = $memoizeMapper($mapping);

                                    $termValue = Str::lower($term->name);
                                    $job_taxonomy_terms[$taxonomy_name] = $raw;

                                    /**
                                     * compare retrieved value with this term
                                     */
                                    if (!is_array($mysgsValue)) {
                                        //logger('converting mysgs value to array');
                                        $mysgsValue = [$mysgsValue];
                                    }

                                    foreach ($mysgsValue as $index => $mysgsValue_single) {
                                        $mysgsValue_single = trim($mysgsValue_single);

                                        if (empty($mysgsValue_single)) {
                                            continue;
                                        }
                                        /*logger(sprintf('checking taxo "%s" for term "%s" against mysgs value: "%s"',
                                                $taxonomy_name, $termValue, print_r($mysgsValue_single, true))
                                        );*/
                                        if (!(Str::is($termValue, Str::lower($mysgsValue_single))
                                            || (isset($term->config['aliases'])
                                                && in_array(Str::lower($mysgsValue_single),
                                                    array_map('Str::lower', $term->config['aliases']))
                                            )
                                        )) {
                                            /*logger(sprintf('rule "%s": term "%s" did not match with "%s"',
                                                    $rule->id, $termValue, $mysgsValue_single)
                                            );*/
                                            $matched = false;
                                        } else {
                                            $matchedTaxonomies[$taxonomy_name] = true;

                                            /*logger(sprintf('rule "%s": term "%s" matched with "%s"',
                                                    $rule->id, $termValue, $mysgsValue_single)
                                            );*/

                                            if (!in_array($term->name,
                                                $job_taxonomy_terms_matches[$taxonomy_name])) {
                                                $job_taxonomy_terms_matches[$taxonomy_name][] = $term->name;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $taxonomyMatch = true;
                        //logger('$matchedTaxonomies: ' . print_r($matchedTaxonomies, true));
                        foreach ($matchedTaxonomies as $taxonomy => $state) {
                            $taxonomyMatch &= $state;
                        }

                        if ($matched || $taxonomyMatch) {
                            $clientRules[] = $rule;
                            /*logger(sprintf('rule %s added, matched=%b  taxoMatch=%b ',
                                    $rule->id, $matched, $taxonomyMatch)
                            );*/
                        }
                    }

                    logger('filtering done in '.microtime(true) - $start);

                    /*
                     * Fill in any unused taxonomy, for display in job identification section
                     */
                    /** @var Taxonomy $taxonomy */
                    foreach ($client->account_structure_child_taxonomies as $taxonomy) {
                        if (!in_array($taxonomy->name, $job_taxonomy_terms) && $taxonomy->mappings()->count()) {

                            foreach ($taxonomy->mappings as $mapping) {
                                //logger('fill using mapping '.$mapping->id);
                                list($mysgsValue, $raw) = $memoizeMapper($mapping);

                                if ($mysgsValue) {

                                    if (!is_array($mysgsValue)) {
                                        //logger('converting mysgs value to array');
                                        $mysgsValue = [$mysgsValue];
                                    }

                                    foreach ($mysgsValue as $index => $mysgsValue_single) {
                                        //logger('mysgsvalue:'.print_r($mysgsValue_single, true));
                                        if (!isset($job_taxonomy_terms[$taxonomy->name]) || !in_array($mysgsValue_single,
                                                $job_taxonomy_terms[$taxonomy->name])) {
                                            $job_taxonomy_terms[$taxonomy->name][] = $mysgsValue_single;
                                        }
                                        $job_taxonomy_terms_extra[$taxonomy->name][$mapping->slug.'/'.$mapping->field_path] = $mysgsValue_single;
                                    }

                                }
                            }

                        }
                    }

                    $metadata->job_taxonomy = $job_taxonomy_terms;
                    //$metadata->job_taxonomy_extra = $job_taxonomy_terms_extra;
                    $metadata->matched_taxonomy = $job_taxonomy_terms_matches;

                    $job->metadata = $metadata;
                    $job->save();

                    return $clientRules;
                });
        }

        return $cached_rules;

    }

}
