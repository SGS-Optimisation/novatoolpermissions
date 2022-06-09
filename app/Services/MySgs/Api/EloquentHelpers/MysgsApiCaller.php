<?php


namespace App\Services\MySgs\Api\EloquentHelpers;


use App\Models\FieldMapping;
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
        logger('generating api url call '.$apiName.'::'.$apiAction.' on job '.$this->job->id);

    }


    /**
     * @param $apiName
     * @param $apiAction
     * @return MysgsApiCaller
     */
    public function handle($apiName, $apiAction = null, $apiParams = null)
    {
        $fieldMapping = null;
        if (!is_string($apiName) && get_class($apiName) == FieldMapping::class) {
            $fieldMapping = $apiName;

            $apiName = $fieldMapping->api_name;
            $apiAction = $fieldMapping->api_action;
            $apiParams = $fieldMapping->api_params;
        }

        logger('handling api call '.$apiName.'::'.$apiAction.' on job '.$this->job->id);

        $api_class = 'App\Services\MySgs\Api\\'.$apiName;
        $api = new $api_class;
        $id_param = static::getApiIdParam($apiName, $apiAction, $this->job);


        $this->response = $api::$apiAction($id_param, $apiParams);

        $job_metadata = $this->job->metadata;

        if ($fieldMapping) {
            $job_metadata->{$fieldMapping->title} = $this->response;
        } else {
            $job_metadata->$apiAction = $this->response;
        }

        $this->job->metadata = $job_metadata;
        $this->job->save();

        return $this;
    }


    public static function getApiIdParam($apiName, $function, $job)
    {
        /*
         * Build the appropriate Api object
         */
        $apiclass = 'App\Services\MySgs\Api\\'.$apiName;
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
