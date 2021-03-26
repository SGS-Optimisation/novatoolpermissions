<?php

namespace App\Listeners\Jobs;

use App\Events\Jobs\NewJobSearched;
use App\Services\MySgs\DataLoader;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoadMySgsData implements ShouldQueue
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
        DataLoader::handle($event->job);
    }
}
