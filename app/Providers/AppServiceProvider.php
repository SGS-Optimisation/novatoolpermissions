<?php

namespace App\Providers;

use App\Models\Team;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment(['local'])) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Metrogistics\AzureSocialite\UserFactory::userCallback(function($new_user){
            $new_user->save();

            $new_user->ownedTeams()->save(Team::forceCreate([
                'user_id' => $new_user->id,
                'name' => explode(' ', $new_user->name, 2)[0]."'s Team",
                'personal_team' => true,
            ]));
        });
    }
}
