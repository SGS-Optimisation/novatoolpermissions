<?php


namespace App\Services\Job;


use App\Models\Job;
use App\Services\MySgs\Api\JobApi;

class JobApiHandler
{
    /**
     * @param $jobNumber
     * @param $apiName
     * @return Job
     */
    public static function handle($jobNumber, $apiName){

        $job = Job::whereJobNumber($jobNumber)->first();

        if($job){
            return $job;
        }

        $jobDetails = JobApi::$apiName($jobNumber);

        Job::create([
            'job_number' => $jobNumber,
            'metadata' => $jobDetails
        ]);

        return $job;
    }

}
