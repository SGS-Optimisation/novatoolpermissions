<?php

namespace App\Providers;

//use App\Listeners\AuditedListener;
use App\Models\ClientAccount;
use App\Models\Rule;
use App\Observers\ClientAccountObserver;
use App\Observers\RuleObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Jetstream\Events\TeamMemberAdded;
use OwenIt\Auditing\Events\Audited;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        'App\\Events\\Rules\\Flagged' => [
            'App\\Listeners\\Rules\\RebuildRuleCache',
            'App\\Listeners\\Rules\\NotifyRuleContributor',
            'App\\Listeners\\Rules\\NotifyTeamOwner',
        ],

        'App\\Events\\Rules\\Unflagged' => [
            'App\\Listeners\\Rules\\RebuildRuleCache',
        ],

        'App\\Events\\Rules\\RuleUpdated' => [
            'App\\Listeners\\Rules\\RebuildRuleCache',
            'App\\Listeners\\Rules\\AddUserAsContributor',
            'App\\Listeners\\Rules\\AddTeamAsContributor',
        ],

        'App\\Events\\Rules\\Deleted' => [
            'App\\Listeners\\Rules\\RebuildRuleCache',
        ],

        'App\\Events\\Jobs\\NewJobSearched' => [
            'App\\Listeners\\Jobs\\LoadMySgsData',
        ],

        'App\\Events\\Jobs\\JobLoaded' => [

        ],

        'App\\Events\\ClientAccounts\\TermsUpdated' => [
        ],

        TeamMemberAdded::class => [
            'App\\Listeners\\Users\\GiveUserRoles'
        ],

        'App\\Events\\Jobs\\ClientAccountNotMatched' => [
            'App\\Listeners\\Jobs\\NotifyAdminsClientAccountNotMatched',
        ],
//        Audited::class => [
//            AuditedListener::class
//        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        ClientAccount::observe(ClientAccountObserver::class);
        Rule::observe(RuleObserver::class);
    }

}
