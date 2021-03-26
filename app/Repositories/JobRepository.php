<?php


namespace App\Repositories;


use App\Events\Jobs\NewJobSearched;
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
            ],
        ]);
    }

    public static function findByJobNumber($job_number)
    {
        $job = Job::whereJobNumber($job_number)->first();

        if (!$job) {
            $job = static::createFromJobNumber($job_number);

            event(new NewJobSearched($job));
        }

        return $job;


    }
}
