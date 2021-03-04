<?php

namespace Database\Seeders;

use App\Models\MysgsClientAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class MysgsClientAccountSeeder extends Seeder
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
            MysgsClientAccount::firstOrCreate([
                'name' => $customerNmae,
                'alias' => implode(PHP_EOL, array_unique(Arr::pluck($customer, 'Customer Name')))
            ]);
        }
    }
}
