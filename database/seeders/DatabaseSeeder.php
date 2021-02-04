<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        if (app()->environment() === 'local') {

            $user = User::firstOrCreate([
                'name' => 'Quidam',
                'email' => 'quidam@sgsco.com',
                'password' => bcrypt('letmein'),
            ]);

            $admin = User::firstOrCreate([
                'name' => 'Admin',
                'email' => 'admin@sgsco.com',
                'password' => bcrypt('letmein'),
            ]);
        }

        $this->call(TaxonomySeeder::class);
        $this->call(ClientAccountSeeder::class);

        if (app()->environment() === 'local') {

            $admin->teams()->create([
                'name' => 'Admin\'s Team',
                'user_id' => $admin->id,
                'client_account_id' => ClientAccount::first()->id,
                'personal_team' => true,
            ]);

            $user->teams()->create([
                'name' => 'Quidam\'s Team',
                'user_id' => $user->id,
                'client_account_id' => ClientAccount::first()->id,
                'personal_team' => true,
            ]);
        }

        // \App\Models\User::factory(10)->create();


        $this->call(RuleSeeder::class);

    }
}
