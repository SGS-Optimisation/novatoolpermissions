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


        $this->call(ClientAccountSeeder::class);
        $this->call(TaxonomySeeder::class);

        if (app()->environment() === 'local') {

            /** @var ClientAccount $unilever */
            $unilever = ClientAccount::whereRaw('LOWER(alias) LIKE "%unilever%"')->first();
            $gsk = ClientAccount::whereRaw('LOWER(alias) LIKE "%gsk%"')->first();
            $npp = ClientAccount::whereRaw('LOWER(alias) LIKE "%nestle%"')->first();

            $admin->teams()->create([
                'name' => 'Admin\'s Team',
                'user_id' => $admin->id,
                'client_account_id' => null,
                'personal_team' => true,
            ]);

            $user->teams()->create([
                'name' => 'Quidam\'s Team',
                'user_id' => $user->id,
                'client_account_id' => null,
                'personal_team' => true,
            ]);

            $admin->teams()->sync([$unilever->team->id, $gsk->team->id, $npp->team->id]);
        }

        $this->call(RuleSeeder::class);
    }
}
