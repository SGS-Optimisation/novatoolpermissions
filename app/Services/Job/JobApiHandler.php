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
        logger('handling api call ' . $apiName . ' on job ' . $this->job->id);


        $this->response = JobApi::$apiName($this->getApiParam($apiName));

        $job_metadata = $this->job->metadata;

        $job_metadata->$apiName = $this->response;

        $this->job->metadata = $job_metadata;
        $this->job->save();

        return $this;
    }


    protected function getApiParam($apiName) {
        /*
         * Build the appropriate Api object
         */
        $apiclass = 'App\Services\MySgs\Api\JobApi';
        $api = new $apiclass;
        $function = $apiName;

        /*
         * Check which parameter is expected from the api function
         */
        $signature = get_func_argNames($function, $api);

        if (array_keys($signature)[0] == 'jobVersionId') {
            $param = $this->job->metadata->basicInfo->jobVersionId;
        } else {
            $param = $this->job->job_number;
        }

        return $param;
    }

}
