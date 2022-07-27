<?php

namespace App\Listeners\Jobs;

use App\Events\Jobs\ClientAccountNotMatched;
use App\Models\User;
use App\Notifications\ClientAccountNotMatchedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

class NotifyAdminsClientAccountNotMatched implements ShouldQueue
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
     * @param  \App\Events\Jobs\ClientAccountNotMatched  $event
     * @return void
     */
    public function handle(ClientAccountNotMatched $event)
    {
        $maintainers = User::withRoles()
            ->whereHas('roles.getPermissions',
                function ($q) {
                    return $q->where('permission_slug', 'receivesErrorNotifications');
                })
            ->get();

        $executed = RateLimiter::attempt(
            'notify-unmatched-'.$event->enduser_name,
            $perMinute = 1,
            function() use($maintainers, $event) {
                Notification::send(
                    $maintainers,
                    new ClientAccountNotMatchedNotification($event->enduser_name)
                );
            },
            $decaySeconds = 600
        );

        if (! $executed) {
            logger('notifications for ' . $event->enduser_name . ' already sent in the last 10min');
        }

    }
}
