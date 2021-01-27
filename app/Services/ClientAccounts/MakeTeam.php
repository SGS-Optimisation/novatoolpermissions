<?php


namespace App\Services\ClientAccounts;


use App\Models\User;

class MakeTeam extends BaseClientAccountService
{


    public function handle()
    {
        $this->clientAccount->team()->create([
            'name' => $this->clientAccount->name . '\'s Team',
            'personal_team' => false,
            'user_id' => (auth()->guest() ? User::first()->id : auth()->user()->id),
        ]);
    }
}
