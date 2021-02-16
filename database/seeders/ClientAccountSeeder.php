<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use App\Services\ClientAccounts\LegacyImport;
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

        if (app()->environment() === 'production') {
            /**
             * importing legacy data from mongo dump
             */
            LegacyImport::handle();
        } else {
            ClientAccount::create([
                'name' => 'Unilever',
            ]);

            ClientAccount::create([
                'name' => 'Nestle Purina',
            ]);
        }
    }
}
