<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Jetstream;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return mixed
     */
    public function create($user, array $input)
    {
        Gate::forUser($user)->authorize('create', Jetstream::newTeamModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'owner_id' => ['int'],
        ])->validateWithBag('createTeam');

        if (isset($input['owner_id'])) {
            $owner = User::find($input['owner_id']);
        } else {
            $owner = $user;
        }

        return $owner->ownedTeams()->create([
            'name' => $input['name'],
            'region' => $input['region'],
            'client_account_id' => $input['client_account_id'],
            'personal_team' => false,
        ]);
    }
}
