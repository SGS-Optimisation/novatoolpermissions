<?php


namespace App\Services\Rule;


use App\Models\ClientAccount;
use App\Models\Job;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\MySgs\Api\EloquentHelpers\JobClientAccountMatcher;
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
        $memoizeMapper = memoize(
            function ($job, $mapping) {
                $mapper = new JobFieldsMapper($job, $mapping);
                return $mapper->run();
            }
        );

        $clientRules = [];

        if (!$job->metadata->client_found) {
            logger('no client account associated job, searching');

            (new JobClientAccountMatcher($job))->handle();

        }

        if ($job->metadata->client_found) {

            $client = ClientAccount::find($job->metadata->client->id);
            logger('loaded client '.$client->name);

            $metadata = $job->metadata;

            $job_taxonomy_terms_matches = [];
            $job_taxonomy_terms = [];

            /*
             *  Match rules against job's metadata
             */
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
                     * If a rule's taxo term applies to any/all jobs, it should be displayed, skip further checks
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
                        if (!is_array($mysgsValue)) {
                            //logger('converting mysgs value to array');
                            $mysgsValue = [$mysgsValue];
                        }

                        foreach ($mysgsValue as $index => $mysgsValue_single) {
                            if (empty($mysgsValue_single)) {
                                continue;
                            }
                            /*logger(sprintf('checking taxo %s for term "%s" against mysgs value: %s',
                                    $term->taxonomy->name, $termValue, print_r($mysgsValue_single, true))
                            );*/
                            if (!(Str::is($termValue, Str::lower($mysgsValue_single))
                                || (isset($term->config['aliases'])
                                    && in_array($mysgsValue_single, array_map('Str::lower', $term->config['aliases']))
                                )
                            )) {
                                /*logger(sprintf('rule %s dropped, term %s did not match with %s',
                                        $rule->id, $termValue, $mysgsValue_single)
                                );*/
                                $matched = false;
                            } else {
                                $matchedTaxonomies[$term->taxonomy->name] = true;

                                /*logger(sprintf('rule %s added, term %s matched with %s',
                                        $rule->id, $termValue, $mysgsValue_single)
                                );*/

                                if (!in_array($term->name, $job_taxonomy_terms_matches[$term->taxonomy->name])) {
                                    $job_taxonomy_terms_matches[$term->taxonomy->name][] = $term->name;
                                }
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
                } else {
                    /*logger(sprintf('rule %s dropped, matched=%b  taxoMatch=%b ',
                            $rule->id, $matched, $taxonomyMatch)
                    );*/
                }
            }

            /*
             * Fill in any unused taxonomy, for display in job identification section
             */
            /** @var Taxonomy $taxonomy */
            foreach ($client->child_taxonomies as $taxonomy) {
                if (!in_array($taxonomy->name, $job_taxonomy_terms) && $taxonomy->mapping) {
                    list($mysgsValue, $raw) = $memoizeMapper($job, $taxonomy->mapping);

                    $job_taxonomy_terms[$taxonomy->name] = $mysgsValue;
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
