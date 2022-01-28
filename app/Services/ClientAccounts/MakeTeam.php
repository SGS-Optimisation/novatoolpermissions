<?php


namespace App\Services\ClientAccounts;


use App\Models\User;

class MakeTeam extends BaseClientAccountService
{

    public function handle($team_owner = null)
    {
        if($this->clientAccount->team) {
            return;
        }

        if($team_owner && is_int($team_owner)) {
            $team_owner = User::find($team_owner);
        }

        if(!$team_owner) {
            $team_owner = (auth()->guest() ? User::first() : auth()->user());

            if (!in_array('team-leader', $team_owner->roles->pluck('slug')->all())) {
                $team_owner->assignRole('team-leader');
            }
        }

        \Log::debug('making new team with owner ' . $team_owner->name);

        $this->clientAccount->team()->create([
            'name' => $this->clientAccount->name . '\'s Team',
            'personal_team' => false,
            'user_id' => $team_owner->id,
        ]);
    }
}
