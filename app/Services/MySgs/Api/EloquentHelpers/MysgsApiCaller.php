<?php


namespace App\Services\MySgs\Api\EloquentHelpers;


use App\Models\Job;
use App\Repositories\JobRepository;

class MysgsApiCaller
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
            $job = JobRepository::findByJobNumber($job);
        }

        $this->job = $job;
    }


    public function generateUrl($apiName, $apiAction)
    {
        logger('generating api url call ' . $apiName  . '::' . $apiAction . ' on job ' . $this->job->id);

    }


    /**
     * @param $apiName
     * @param $apiAction
     * @return MysgsApiCaller
     */
    public function handle($apiName, $apiAction)
    {
        logger('handling api call ' . $apiName  . '::' . $apiAction . ' on job ' . $this->job->id);

        $apiclass = 'App\Services\MySgs\Api\\' . $apiName;
        $api = new $apiclass;
        $param = static::getApiParam($apiName, $apiAction, $this->job);


        $this->response = $api::$apiAction($param);

        $job_metadata = $this->job->metadata;

        $job_metadata->$apiAction = $this->response;

        $this->job->metadata = $job_metadata;
        $this->job->save();

        return $this;
    }


    public static function getApiParam($apiName, $function, $job) {
        /*
         * Build the appropriate Api object
         */
        $apiclass = 'App\Services\MySgs\Api\\' . $apiName;
        $api = new $apiclass;

        /*
         * Check which parameter is expected from the api function
         */
        $signature = get_func_argNames($function, $api);

        if (array_keys($signature)[0] == 'jobVersionId') {
            $param = $job->metadata->basicInfo->jobVersionId;
        } else {
            $param = $job->job_number;
        }

        return $param;
    }

}
