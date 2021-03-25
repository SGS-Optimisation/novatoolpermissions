<?php

namespace App\Listeners\Rules;

use App\Events\Rules\Flagged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyTeamOwner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Flagged  $event
     * @return void
     */
    public function handle(Flagged $event)
    {
        //
    }
}
