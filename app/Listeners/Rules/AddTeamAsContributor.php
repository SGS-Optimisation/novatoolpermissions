<?php

namespace App\Listeners\Rules;

use App\Events\Rules\RuleUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddTeamAsContributor
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
     * @param  RuleUpdated  $event
     * @return void
     */
    public function handle(RuleUpdated $event)
    {
        if (!$event->user) {
            return;
        }

        if ($event->user->belongsToOneOfClientTeams($event->rule->clientAccount)) {
            $team = $event->user->allTeams()->firstWhere('client_account_id', $event->rule->client_account_id);

            $event->rule->teams()->syncWithoutDetaching([$team->id]);
        }
    }
}
