<?php

namespace App\Providers;

use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\Taxonomy;
use App\Models\Team;
use App\Models\Term;
use App\Policies\ClientAccountPolicy;
use App\Policies\RulePolicy;
use App\Policies\TaxonomyPolicy;
use App\Policies\TeamPolicy;
use App\Policies\TermPolicy;
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
        Taxonomy::class => TaxonomyPolicy::class,
        Term::class => TermPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        collect([
            'forceCreateRules',
            'createRules', 'updateRules', 'deleteRules', 'publishRules',
            'createClientAccounts', 'updateClientAccounts', 'deleteClientAccounts',
            'accessPM',
            'accessStats',
            'accessBackend',
            'viewTaxonomies', 'manageTaxonomies',
            'viewTerms', 'manageTerms',
            'viewFieldMappings', 'manageFieldMappings',
            'manageSettings',
            'manageTeams',

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
