<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(10)->create()->each(function($user){
            $user->teams()->create([
                'name' => $user->name.'\'s Team',
                'user_id' => $user->id,
                'client_account_id' => null,
                'personal_team' => true,
            ]);

            ClientAccount::inRandomOrder()->take(3)->get()->each(function(ClientAccount $client) use ($user){
                 $client->team->users()->attach($user->id);
            });
        });
    }
}
