<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use Illuminate\Database\Seeder;

class ClientAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ClientAccount::create([
            'name' => 'Unilever',
        ]);

        ClientAccount::create([
            'name' => 'Nestle Purina',
        ]);
    }
}
