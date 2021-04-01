<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use App\Models\Team;
use App\Models\User;
use App\Services\LegacyImport\TaxonomyLegacyImport;
use Illuminate\Database\Seeder;
use OptimistDigital\NovaSettings\NovaSettings;

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
            $admin = User::firstOrCreate([
                'name' => 'Admin',
                'email' => 'admin@sgsco.com',
                'password' => bcrypt('letmein'),
            ]);

            $user = User::firstOrCreate([
                'name' => 'Quidam',
                'email' => 'quidam@sgsco.com',
                'password' => bcrypt('letmein'),
            ]);


            $settings = [
                [
                    'key' => 'api_app_id',
                    'value' => env('MYSGS_API_APP_ID')
                ],
                [
                    'key' => 'api_app_key',
                    'value' => env('MYSGS_API_APP_KEY')
                ],
                [
                    'key' => 'api_base_path',
                    'value' => 'https://sgscoapimanagement.azure-api.net/{api_name}/api/v{version}/'
                ],
                [
                    'key' => 'api_version',
                    'value' => '1.0'
                ],
                [
                    'key' => 'subscription_key',
                    'value' => env('MYSGS_API_SUBSCRIPTION_KEY'),
                ]
            ];

            foreach ($settings as $setting) {
                NovaSettings::setSettingValue($setting['key'], $setting['value']);
            }
        }


        $this->call(MysgsClientAccountSeeder::class);

        $this->call(TaxonomyAccountStructureSeeder::class);
        $this->call(TaxonomyJobCategorizationsSeeder::class);
        //(new TaxonomyLegacyImport)->handle();
        //$this->call(ClientAccountSeeder::class);
        (new \App\Services\LegacyImport\ClientAccountLegacyImport())->handle();
        $this->call(FieldMappingSeeder::class);

        if (app()->environment() === 'local') {

            /** @var ClientAccount $unilever */
            $unilever = ClientAccount::whereRaw('LOWER(name) LIKE "%unilever%"')->first();
            $gsk = ClientAccount::whereRaw('LOWER(name) LIKE "%gsk%"')->first();
            $npp = ClientAccount::whereRaw('LOWER(name) LIKE "%nestle%"')->first();

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

            $admin->teams()->syncWithPivotValues(
                [$unilever->team->id, $gsk->team->id, $npp->team->id],
                ['role' => 'admin']
            );
        }

        (new \App\Services\LegacyImport\RuleLegacyImport())->handle();
    }
}
