<?php

namespace App\Listeners\Rules;

use App\Events\Rules\Flagged;
use App\Notifications\FlaggedRuleNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyRuleContributor implements ShouldQueue
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
        Notification::send($event->rule->contributors, new FlaggedRuleNotification($event->rule));
    }
}
