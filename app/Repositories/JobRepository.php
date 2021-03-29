<?php


namespace App\Repositories;


use App\Events\Jobs\NewJobSearched;
use App\Listeners\Jobs\LoadMySgsData;
use App\Models\Job;

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
            ],
        ]);
    }

    public static function findByJobNumber($job_number)
    {
        $job = Job::whereJobNumber($job_number)->first();

        if (!$job) {
            $job = static::createFromJobNumber($job_number);

            event(new NewJobSearched($job));
        } elseif ($job->metadata->processing_mysgs) {
            logger($job_number . ' still processing, adding to queue, if running it will not re-trigger');
            event(new NewJobSearched($job));
        }

        return $job;


    }
}
