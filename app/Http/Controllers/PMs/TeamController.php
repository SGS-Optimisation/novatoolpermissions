<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Http\Controllers\Inertia\TeamController as InertiaTeamController;
use Laravel\Jetstream\Jetstream;

class TeamController extends Controller
{
    /**
     * Show the team management screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Inertia\Response
     */
    public function show(Request $request, $client_account_slug, $teamId)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        Gate::authorize('view', $team);

        return Jetstream::inertia()->render($request, 'Teams/Show', [
            'clientAccount' => $client_account,
            'team' => $team->load('owner', 'users', 'teamInvitations'),
            'availableRoles' => array_values(Jetstream::$roles),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
            'permissions' => [
                'canAddTeamMembers' => Gate::check('addTeamMember', $team),
                'canDeleteTeam' => Gate::check('delete', $team),
                'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                'canUpdateTeam' => Gate::check('update', $team),
            ],
        ]);
    }

    /**
     * Show the team creation screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        $this->authorize('create', ClientAccount::class);

        $users = User::select(['name', 'id', 'profile_photo_path'])->orderBy('name', 'asc')->get();

        return Inertia::render('Teams/Create', [
            'clientAccount' => $client_account,
            'users' => $users,
        ]);
    }

    /**
     * Create a new team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateTeamRequest $request, $client_account_slug)
    {
        $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        $this->authorize('create', $client_account);

        $owner = User::withRoles()->find($request->get('owner_id'));

        if (!in_array('team-leader', $owner->roles->pluck('slug')->all())) {
            $owner->assignRole('team-leader');
        }

        $creator = app(CreatesTeams::class);

        $team = $creator->create($request->user(), $request->all());

        return redirect(route('pm.client-account.teams.show', [$client_account_slug, $team->id]));
    }
}
