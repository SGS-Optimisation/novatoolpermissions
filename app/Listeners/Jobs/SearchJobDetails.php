<?php

namespace App\Listeners\Jobs;

use App\Events\Jobs\NewJobSearched;
use App\Services\Job\JobApiHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SearchJobDetails implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  NewJobSearched  $event
     * @return void
     */
    public function handle($event)
    {
        $job = $event->job;

        $basicInfo = (new JobApiHandler($job))->handle('basicInfo');
        $basicDetails = (new JobApiHandler($job))->handle('basicDetails');
        $extraDetails = (new JobApiHandler($job))->handle('extraDetails');

        $job->designation = $basicInfo->response->jobDescription;
        $job_metadata = $job->metadata;
        $job_metadata->processing_mysgs = false;

        $job->metadata = $job_metadata;
        $job->save();

    }
}
