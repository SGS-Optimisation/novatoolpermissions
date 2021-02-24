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
        $customers = arrayGroupBy(csvToArray(
            storage_path('seed/ClientAccount.csv')),
            'Simplified Group Name'
        );

        // removing first element

        Arr::forget($customers, '---------------------');

        foreach ($customers as $customerNmae => $customer) {
            ClientAccount::firstOrCreate([
                'name' => $customerNmae,
                'alias' => implode(PHP_EOL, array_unique(Arr::pluck($customer, 'Customer Name')))
            ]);
        }

        (new \App\Services\LegacyImport\ClientAccount())->handle();

    }
}
