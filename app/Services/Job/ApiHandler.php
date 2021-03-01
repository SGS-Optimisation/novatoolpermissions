<?php


namespace App\Services\Job;


use App\Models\Job;
use App\Services\MySgs\Api\JobApi;

class ApiHandler
{
    /**
     * @param $jobNumber
     * @return Job
     */
    public static function handle($jobNumber){

        $job = Job::whereJobNumber($jobNumber)->first();

        if($job){
            return $job;
        }

        $jobDetails = JobApi::basicDetails($jobNumber);

        Job::create([
            'job_number' => $jobNumber,
            'metadata' => $jobDetails
        ]);

        return $job;
    }

}
