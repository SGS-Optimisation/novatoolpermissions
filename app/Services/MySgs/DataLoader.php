<?php


namespace App\Services\MySgs;

use App\Models\ClientAccount;
use App\Models\Job;
use App\Operations\Jobs\GetStageOperation;
use App\Services\MySgs\Api\EloquentHelpers\MysgsApiCaller;
use App\Operations\Jobs\MatchClientAccountOperation;
use Illuminate\Support\Str;

class DataLoader
{
    public Job $job;

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
        $basicInfo = (new MysgsApiCaller($this->job))->handle('JobApi', 'basicInfo');

        if (!$basicInfo->response) {
            static::fail('Job not found');
            return;
        }

        /*
         * Pre-call all other endpoints
         */
        $basicDetails = (new MysgsApiCaller($this->job))->handle('JobApi', 'basicDetails');
        $extraDetails = (new MysgsApiCaller($this->job))->handle('JobApi', 'extraDetails');
        $jobContacts = (new MysgsApiCaller($this->job))->handle('JobApi', 'jobContacts');
        $jobItems = (new MysgsApiCaller($this->job))->handle('ProductionApi', 'jobItems');


        static::augmentMeta($basicInfo, $basicDetails, $extraDetails, $jobItems);
    }


    protected function augmentMeta($basicInfo, $basicDetails, $extraDetails, $jobItems)
    {
        $this->job->designation = $basicInfo->response->jobDescription;
        $job_metadata = $this->job->metadata;
        $job_metadata->processing_mysgs = false;

        $this->job->metadata = $job_metadata;
        $this->job->save();

        /** @noinspection PhpExpressionResultUnusedInspection
         * Self invoked class which
         */
        (new MatchClientAccountOperation($this->job))->handle();
        (new GetStageOperation($this->job))->handle();
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
