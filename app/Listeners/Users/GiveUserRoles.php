<?php

namespace App\Listeners\Users;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Jetstream\Events\TeamMemberAdded;

class GiveUserRoles
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
     * @param  TeamMemberAdded  $event
     * @return void
     */
    public function handle(TeamMemberAdded $event)
    {
        /** @var User $user */
        $user = $event->user;

        if (!$user->roles()->count()) {
            $user->assignRole('project-manager');
        }
    }
}
