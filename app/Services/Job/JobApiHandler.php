<?php


namespace App\Services\Job;


use App\Broadcast\RulesFiltered;
use App\Jobs\JobApiRequest;
use App\Models\Job;
use App\Repositories\JobRepository;
use App\Services\MySgs\Api\JobApi;

class JobApiHandler
{
    /**
     * @var Job
     */
    public $job;

    public $response;

    /**
     * JobApiHandler constructor.
     * @param  Job  $job
     */
    public function __construct($job)
    {
        if (is_string($job)) {
            $job = JobRepository::createFromJobNumber($job);
        }

        $this->job = $job;
    }


    /**
     * @param $apiName
     * @return JobApiHandler
     */
    public function handle($apiName)
    {
        logger('handing api call ' . $apiName . ' on job ' . $this->job->id);
        $jobNumber = $this->job->job_number;

        $this->response = JobApi::$apiName($jobNumber);

        $job_metadata = $this->job->metadata;

        $job_metadata->$apiName = $this->response;

        $this->job->metadata = $job_metadata;
        $this->job->save();

        return $this;
    }

}
