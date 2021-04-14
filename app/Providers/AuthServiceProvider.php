<?php

namespace App\Providers;

use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Team;
use App\Policies\ClientAccountPolicy;
use App\Policies\RulePolicy;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Silvanite\Brandenburg\Traits\ValidatesPermissions;

class AuthServiceProvider extends ServiceProvider
{
    use ValidatesPermissions;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
        Rule::class => RulePolicy::class,
        ClientAccount::class => ClientAccountPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        collect([
            'createRules', 'updateRules', 'deleteRules',
            'createClientAccounts', 'updateClientAccounts', 'deleteClientAccounts',

        ])->each(function ($permission) {
            \Gate::define($permission, function ($user) use ($permission) {
                if ($this->nobodyHasAccess($permission)) {
                    return true;
                }

                return $user->hasRoleWithPermission($permission);
            });
        });

        $this->registerPolicies();
    }
}
