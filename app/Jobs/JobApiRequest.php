<?php

namespace App\Jobs;

use App\Broadcast\RulesFiltered;
use App\Models\Job;
use App\Services\MySgs\Api\JobApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobApiRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private string $jobNumber;

    /**
     * @var string
     */
    private string $apiName;

    /**
     * Create a new job instance.
     *
     * @param $jobNumber
     * @param $apiName
     */
    public function __construct($jobNumber, $apiName)
    {
        $this->jobNumber = $jobNumber;
        $this->apiName = $apiName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $job = Job::whereJobNumber($this->jobNumber)->first();

        if($job){
            $jobDetails = JobApi::{$this->apiName}($this->jobNumber);
            $jobMetadata = $job->metadata;
            $jobMetadata->{$this->apiName} = $jobDetails;
            $job->metadata = $jobMetadata;
            $job->save();

            event(new RulesFiltered($job));
        }
    }
}
