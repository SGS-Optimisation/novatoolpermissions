<?php

namespace App\Listeners\Jobs;

use App\Events\Jobs\NewJobSearched;
use App\Models\Job;
use App\Services\MySgs\DataLoader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadMySgsData implements ShouldQueue, ShouldBeUnique
{

    use InteractsWithQueue, Queueable;

    /**
     * @var Job
     */
    public $mysgs_job;

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
        $this->mysgs_job = $event->mysgs_job;

        logger('handling job '.$this->mysgs_job->job_number);

        DataLoader::handle($this->mysgs_job);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->mysgs_job->id;
    }

    /**
     * @param $event
     * @param  \Exception|null  $exception
     */
    public function failed($event, $exception)
    {
        \Log::error('loading mysgs data failed');
        \Log::error($exception->getMessage());
        \Log::error($exception->getTraceAsString());
        $job_metadata = $event->mysgs_job->metadata;
        $job_metadata->error_mysgs = true;
        $event->mysgs_job->metadata = $job_metadata;

        $event->mysgs_job->save();
    }
}
