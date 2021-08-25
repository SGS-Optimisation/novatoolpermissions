<?php


namespace App\Repositories;


use App\Events\Jobs\NewJobSearched;
use App\Listeners\Jobs\LoadMySgsData;
use App\Models\Job;
use Carbon\Carbon;

class JobRepository
{

    public static function createFromJobNumber($job_number)
    {
        return Job::create([
            'job_number' => $job_number,
            'metadata' => [
                'rules' => null,
                'processing_mysgs' => true,
                'error_mysgs' => false,
                'client_found' => false,
                'error_mysgs_reason' => null,
            ],
        ]);
    }

    public static function refreshJobFromJobNumber($job_number)
    {
        $job = Job::whereJobNumber($job_number)->first();

        $metadata = $job->metadata;

        $metadata->processing_mysgs = true;
        $metadata->error_mysgs = false;
        $metadata->error_mysgs_reason = null;

        $job->metadata = $metadata;
        $job->save();

        return $job;
    }

    public static function findByJobNumber($job_number)
    {
        $job = Job::whereJobNumber($job_number)->first();

        if (!$job) {
            $job = static::createFromJobNumber($job_number);
            event(new NewJobSearched($job));

        } elseif ($job->metadata->error_mysgs
            && $job->created_at->lessThan(Carbon::now()->subMinute())
        ) {
            logger($job_number.' was in error, will prune and recreate');
            $job->delete();
            $job = static::createFromJobNumber($job_number);
            event(new NewJobSearched($job));

        } elseif ($job->metadata->processing_mysgs) {
            logger($job_number.' still processing');

        } elseif ($job->updated_at->lessThan(Carbon::now()->subHour())) {
            logger($job_number.' is old, refreshing');
            $job = static::refreshJobFromJobNumber($job_number);
            event(new NewJobSearched($job));
        }

        return $job;
    }
}
