<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;


class ClientAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new \App\Services\LegacyImport\ClientAccountLegacyImport())->handle();
    }
}
