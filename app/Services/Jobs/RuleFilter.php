<?php


namespace App\Services\Jobs;


use App\Models\ClientAccount;
use App\Models\Job;
use App\Models\Rule;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Operations\Jobs\MatchClientAccountOperation;
use App\Services\MySgs\Api\EloquentHelpers\JobFieldsMapper;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RuleFilter
{

    /**
     * @param  Job  $job
     * @return array
     */
    public static function handle(Job $job): array
    {
        $cache_key = 'rules-job-'. $job->job_number;
        $cached_rules = cache($cache_key);

        if($cached_rules === null || !count($cached_rules)) {
            $cached_rules = \Cache::remember($cache_key, 5*60*60, function() use($job) {
                $start = microtime(true);

                $memoizeMapper = memoize(
                    function ($job, $mapping) {
                        $mapper = new JobFieldsMapper($job, $mapping);
                        return $mapper->run();
                    }
                );

                $clientRules = [];

                if (!$job->metadata->client_found) {
                    logger('no client account associated job, searching');

                    (new MatchClientAccountOperation($job))->handle();

                }

                if ($job->metadata->client_found) {

                    $client = ClientAccount::find($job->metadata->client->id);
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
                    foreach ($client->rules()->with(['accountStructureTerms', 'jobCategorizationsTerms'])
                                 ->isPublished()->get() as $rule) {
                        $matched = true;
                        $matchedTaxonomies = [];

                        /** @var Term $term */
                        foreach ($rule->accountStructureTerms as $term) {
                            /*
                             * Initialize array key, there might be many terms for the same taxonomy
                             */
                            if (!Arr::exists($matchedTaxonomies, $term->taxonomy->name)) {
                                $matchedTaxonomies[$term->taxonomy->name] = false;
                            }

                            /*
                             * If a rule's taxo term applies to any/all jobs, it should be displayed, skip further checks
                             */
                            if (Str::lower($term->name) === 'any' || Str::lower($term->name) === 'all') {
                                $matchedTaxonomies[$term->taxonomy->name] = true;
                                continue;
                            }

                            /*
                             * Otherwise, check if the field matches
                             */
                            if ($term->taxonomy->mappings()->count()) {

                                if (!Arr::exists($job_taxonomy_terms_matches, $term->taxonomy->name)) {
                                    $job_taxonomy_terms_matches[$term->taxonomy->name] = [];
                                    $job_taxonomy_terms[$term->taxonomy->name] = [];
                                }

                                foreach ($term->taxonomy->mappings as $mapping) {
                                    //logger('rule comparison using mapping '.$mapping->id);
                                    /**
                                     * retrieve value from mysgs response with help of taxonomy
                                     */
                                    list($mysgsValue, $raw) = $memoizeMapper($job, $mapping);

                                    $termValue = Str::lower($term->name);
                                    $job_taxonomy_terms[$term->taxonomy->name] = $raw;

                                    /**
                                     * compare retrieved value with this term
                                     */
                                    if (!is_array($mysgsValue)) {
                                        //logger('converting mysgs value to array');
                                        $mysgsValue = [$mysgsValue];
                                    }

                                    foreach ($mysgsValue as $index => $mysgsValue_single) {
                                        if (empty($mysgsValue_single)) {
                                            continue;
                                        }
                                        /*logger(sprintf('checking taxo "%s" for term "%s" against mysgs value: "%s"',
                                                $term->taxonomy->name, $termValue, print_r($mysgsValue_single, true))
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
                                            $matchedTaxonomies[$term->taxonomy->name] = true;

                                            /*logger(sprintf('rule "%s": term "%s" matched with "%s"',
                                                    $rule->id, $termValue, $mysgsValue_single)
                                            );*/

                                            if (!in_array($term->name, $job_taxonomy_terms_matches[$term->taxonomy->name])) {
                                                $job_taxonomy_terms_matches[$term->taxonomy->name][] = $term->name;
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
                            logger(sprintf('rule %s added, matched=%b  taxoMatch=%b ',
                                    $rule->id, $matched, $taxonomyMatch)
                            );
                        } else {
                            logger(sprintf('rule %s dropped, matched=%b  taxoMatch=%b ',
                                    $rule->id, $matched, $taxonomyMatch)
                            );
                        }
                    }

                    /*
                     * Fill in any unused taxonomy, for display in job identification section
                     */
                    /** @var Taxonomy $taxonomy */
                    foreach ($client->account_structure_child_taxonomies as $taxonomy) {
                        if (!in_array($taxonomy->name, $job_taxonomy_terms) && $taxonomy->mappings()->count()) {

                            foreach ($taxonomy->mappings as $mapping) {
                                //logger('fill using mapping '.$mapping->id);
                                list($mysgsValue, $raw) = $memoizeMapper($job, $mapping);

                                if ($mysgsValue) {

                                    if (!is_array($mysgsValue)) {
                                        //logger('converting mysgs value to array');
                                        $mysgsValue = [$mysgsValue];
                                    }

                                    foreach ($mysgsValue as $index => $mysgsValue_single) {
                                        //logger('mysgsvalue:'.print_r($mysgsValue_single, true));
                                        if (!isset($job_taxonomy_terms[$taxonomy->name]) || !in_array($mysgsValue_single, $job_taxonomy_terms[$taxonomy->name])) {
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
                }

                logger('filtering done in ' . microtime(true) - $start);

                return $clientRules;
            });
        }

        return $cached_rules;

    }

}
