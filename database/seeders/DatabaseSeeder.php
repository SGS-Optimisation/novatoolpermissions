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

        $this->call(TaxonomySeeder::class);
        $this->call(ClientAccountSeeder::class);

        if (app()->environment() === 'local') {

            $admin = User::firstOrCreate([
                'name' => 'admin',
                'email' => 'admin@sgsco.com',
                'password' => bcrypt('letmein'),
            ]);

            $admin->teams()->create([
                'name' => $admin->name,
                'user_id' => $admin->id,
                'client_account_id' => ClientAccount::first()->id,
                'personal_team' => true,
            ]);
        }

        // \App\Models\User::factory(10)->create();


        $this->call(RuleSeeder::class);

    }
}
