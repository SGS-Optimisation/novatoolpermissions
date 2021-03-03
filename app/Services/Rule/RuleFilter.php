<?php


namespace App\Services\Rule;


use App\Models\ClientAccount;
use App\Models\Job;
use App\Services\MySgs\Mapping\Mapper;
use Illuminate\Support\Str;

class RuleFilter
{

    /**
     * @param Job $job
     * @return array
     */
    public static function handle(Job $job): array
    {

        $clientRules = [];

        if($job->metadata->basicDetails) {
            $client = ClientAccount::whereRaw('LOWER(alias) LIKE "%' . Str::lower($job->metadata->basicDetails->retailer->customerName) . '%"')->first();

            foreach ($client->rules as $rule) {
                $matched = true;
                foreach ($rule->terms as $term) {

                    if (Str::lower($term->name) === 'any' || Str::lower($term->name) === 'all') {

                        continue;
                    }

                    if ($term->taxonomy->mapping) {
                        /**
                         * retrieve value from mysgs response with help of taxonomy
                         * some mapping logic here
                         */

                        $mysgsValue = Str::lower(Mapper::getMetaValue($job, $term->taxonomy->mapping));

                        $termValue = Str::lower($term->name);

                        /**
                         * compare retrieved value with this term
                         */
                        if (! (Str::contains($termValue, $mysgsValue) || Str::contains($mysgsValue, $termValue)) ) {
                            $matched = false;
                        }
                    }

                }

                if ($matched) {
                    $clientRules[] = $rule;
                }
            }
        }

        return $clientRules;
    }

}
