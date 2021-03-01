<?php


namespace App\Services\Rule;


use App\Models\ClientAccount;
use App\Services\Job\JobApiHandler;
use App\Services\MySgs\Api\JobApi;
use App\Services\MySgs\Mapping\Mapper;
use Illuminate\Support\Str;

class Filter
{

    /**
     * @param $jobNumber
     * @return array
     */
    public static function handle($jobNumber): array
    {

        $job = JobApiHandler::handle($jobNumber, 'basicDetails');

        if($job) {

            $client = ClientAccount::whereRaw('LOWER(alias) LIKE "%' . Str::lower($job->metadata->retailer->customerName) . '%"')->first();

            $clientRules = [];

            foreach ($client->rules as $rule) {
                $matched = true;
                foreach ($rule->terms as $term) {

                    if (Str::lower($term->name) === 'any') {
                        $matched = true;
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
                        if (Str::contains($termValue, $mysgsValue) || Str::contains($mysgsValue, $termValue)) {
                            $matched = true;
                        }
                    }

                }

                if ($matched) {
                    $clientRules[] = $rule;
                }
            }

            return $clientRules;
        }

        return [];
    }

}
