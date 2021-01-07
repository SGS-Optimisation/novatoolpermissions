<?php

namespace Database\Seeders;

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
        if(app()->environment() === 'local') {

            User::create([
                'name' => 'admin',
                'email' => 'admin@sgsco.com',
                'password' => bcrypt('letmein'),
            ]);
        }

        // \App\Models\User::factory(10)->create();
        $this->call(ClientAccountSeeder::class);
        $this->call(TaxonomySeeder::class);
        $this->call(RuleSeeder::class);



    }
}
