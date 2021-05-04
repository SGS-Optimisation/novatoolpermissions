<?php


namespace App\Services\MySgs;

use App\Models\ClientAccount;
use App\Models\Job;
use App\Services\MySgs\Api\EloquentHelpers\JobApiCaller;
use App\Services\MySgs\Api\EloquentHelpers\JobClientAccountMatcher;
use Illuminate\Support\Str;

class DataLoader
{
    public $job;

    /**
     * DataLoader constructor.
     * @param $job
     */
    public function __construct($job)
    {
        $this->job = $job;
    }


    public function handle()
    {
        /*
         * Essential as this returns the jobVersionId, required for various other endpoints
         */
        $basicInfo = (new JobApiCaller($this->job))->handle('JobApi', 'basicInfo');

        if (!$basicInfo->response) {
            static::fail('Job not found');
            return;
        }

        /*
         * Pre-call all other endpoints
         */
        $basicDetails = (new JobApiCaller($this->job))->handle('JobApi', 'basicDetails');
        $extraDetails = (new JobApiCaller($this->job))->handle('JobApi', 'extraDetails');
        $jobItems = (new JobApiCaller($this->job))->handle('ProductionApi', 'jobItems');


        static::augmentMeta($this->job, $basicInfo, $basicDetails, $extraDetails, $jobItems);
    }


    protected function augmentMeta($job, $basicInfo, $basicDetails, $extraDetails, $jobItems)
    {
        $job->designation = $basicInfo->response->jobDescription;
        $job_metadata = $job->metadata;
        $job_metadata->processing_mysgs = false;

        $job->metadata = $job_metadata;
        $job->save();

        /** @noinspection PhpExpressionResultUnusedInspection
         * Self invoked class which
         */
        (new JobClientAccountMatcher($this->job))->handle();
    }

    protected function fail($reason)
    {
        $job_metadata = $this->job->metadata;
        $job_metadata->processing_mysgs = false;
        $job_metadata->error_mysgs = true;
        $job_metadata->error_mysgs_reason = $reason;
        $this->job->metadata = $job_metadata;
        $this->job->save();
    }
}
