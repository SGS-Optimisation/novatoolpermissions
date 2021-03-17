<?php

namespace Database\Seeders;

use App\Models\ClientAccount;
use App\Models\Team;
use App\Models\User;
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

            $settings = [
                [
                    'key' => 'api_app_id',
                    'value' => '3CCE21AF-BF21-4B74-9E60-0D1B6BDD6597'
                ],
                [
                    'key' => 'api_app_key',
                    'value' => '&a+!a9t6*&N*Xs%5Q&Qz7_^B3=y-JB2h7&NuLtLaafhuA-TN_p^9^=gYzhsDyp4KkWDykNLn+aW+SZ@X5!PV-V#mSNXpj=&j?aAvtg8q6y-FPHLztdwkn$!E*W@NvW&Xsj7*zQN#+cDsa9#FtMa8yaqs8vzFjY8XwpdhJ6SXgRgu_wytgu=6Jsgad9=uT7=g^QFKMCQzjv9Y7Pgh6bFTP?mayuZArWHk$cq+b=j8uwywDJX8H^dN44NUTeZ^NzeD'
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
                    'value' => '0a5af6485db34ed490ca01b314028d61'
                ]
            ];

            foreach ($settings as $setting){
                NovaSettings::setSettingValue($setting['key'], $setting['value']);
            }
        }


        $this->call(MysgsClientAccountSeeder::class);
        $this->call(ClientAccountSeeder::class);
        $this->call(TaxonomySeeder::class);
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

        $this->call(RuleSeeder::class);
    }
}
