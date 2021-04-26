<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use App\Models\Team;
use App\Models\User;
use App\Services\LegacyImport\ClientAccountLegacyImport;
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
        $this->call(RoleSeeder::class);

        $admin = User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@sgsco.com',
            'password' => bcrypt('letmein'),
        ]);

        $admin->teams()->create([
            'name' => 'Admin\'s Team',
            'user_id' => $admin->id,
            'client_account_id' => null,
            'personal_team' => true,
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


        $this->call(MysgsClientAccountSeeder::class);

        $this->call(TaxonomyAccountStructureSeeder::class);
        $this->call(TaxonomyJobCategorizationsSeeder::class);
        //$this->call(ClientAccountSeeder::class);
        (new ClientAccountLegacyImport())->handle();
        $this->call(FieldMappingSeeder::class);


        (new \App\Services\LegacyImport\RuleLegacyImport())->handle();
    }
}
