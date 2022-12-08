<?php


namespace App\Repositories;


use App\Events\Jobs\NewJobSearched;
use App\Listeners\Jobs\LoadMySgsData;
use App\Models\Job;
use App\Services\Jobs\RuleFilter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class JobRepository
{

    protected static function createFromJobNumber($job_number)
    {
        return Job::create([
            'job_number' => $job_number,
            'metadata' => [
                'rules' => null,
                'processing_mysgs' => true,
                'error_mysgs' => false,
                'error_mysgs_reason' => null,
                'client_found' => false,
                'error_reason' => null,
                'allow_account_selection' => false,
                'possible_accounts' => null,
            ],
        ]);
    }

    protected static function refreshJobFromJobNumber($job_number)
    {
        $job = Job::whereJobNumber($job_number)->first();

        $metadata = $job->metadata;

        $metadata->processing_mysgs = true;
        $metadata->error_mysgs = false;
        $metadata->error_mysgs_reason = null;
        $metadata->client_found = null;
        $metadata->allow_account_selection = false;
        $metadata->possible_accounts = null;

        $job->metadata = $metadata;
        $job->save();
        event(new NewJobSearched($job));

        return $job;
    }

    /**
     * @param $job_number
     * @return Job|null
     */
    public static function findByJobNumber($job_number, $force_clear = false)
    {
        $cache_key = 'rules-job-'. $job_number;

        if($force_clear) {
            logger('forcing clear on ' . $job_number);
            Cache::forget($cache_key);
            Cache::forget(RuleFilter::FILTER_MODE_PROD.'-rules-job-'.$job_number);
            Cache::forget(RuleFilter::FILTER_MODE_PM.'-rules-job-'.$job_number);

            return static::refreshJobFromJobNumber($job_number);
        }

        $job = Job::whereJobNumber($job_number)->first();

        if (!$job) {
            $job = static::createFromJobNumber($job_number);
            event(new NewJobSearched($job));

        } elseif (($job->metadata->error_mysgs || !$job->metadata->client_found)
            && $job->created_at->lessThan(Carbon::now()->subMinute())
        ) {
            logger($job_number.' was in error, will prune and recreate');


            Cache::forget($cache_key);

            $job->delete();
            $job = static::createFromJobNumber($job_number);
            event(new NewJobSearched($job));

        } elseif ($job->metadata->processing_mysgs) {
            logger($job_number.' still processing');

        } elseif ($job->updated_at->lessThan(Carbon::now()->subHour())) {
            logger($job_number.' is old, refreshing');
            $job = static::refreshJobFromJobNumber($job_number);
        }

        return $job;
    }
}
