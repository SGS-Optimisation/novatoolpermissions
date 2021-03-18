<?php

namespace App\Providers;

//use App\Listeners\AuditedListener;
use App\Models\ClientAccount;
use App\Observers\ClientAccountObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
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
    }

}
