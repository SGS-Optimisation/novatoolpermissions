<?php

namespace App\Providers;

use Anaseqal\NovaImport\NovaImport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use OptimistDigital\NovaSettings\NovaSettings;
use Silvanite\NovaToolPermissions\NovaToolPermissions;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        NovaSettings::addSettingsFields([

            Text::make('Api Base Path', 'api_base_path')->required(),

            Text::make('Api Version', 'api_version')->required(),

            Text::make('Api App Id', 'api_app_id')->required(),

            Text::make('Api App Key', 'api_app_key')->required(),

            Text::make('Subscription Key', 'subscription_key')->required()

        ]);
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return Str::endsWith($user->email, '@sgsco.com')
                || Str::endsWith($user->email, '@thr3dcgi.com')
                || $user->hasRoleWithPermission('viewNova');
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new Help,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        $dev_tools = [];

        if (app()->environment() == 'local') {
        }

        return array_merge([
            new NovaToolPermissions(),
            new \OptimistDigital\NovaSettings\NovaSettings
        ], $dev_tools);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
