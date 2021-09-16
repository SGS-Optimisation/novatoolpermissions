<?php

namespace App\Providers;

use Anaseqal\NovaImport\NovaImport;
use App\Models\ClientAccount;
use App\Nova\Dashboards\ClientDashboard;
use App\Nova\Metrics\FlaggedRules;
use App\Nova\Metrics\JobsTrend;
use App\Nova\Metrics\NewUsers;
use App\Nova\Metrics\PublishedRules;
use App\Nova\Metrics\RulesPerAccount;
use App\Nova\Metrics\RulesPerWeek;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use OptimistDigital\NovaSettings\NovaSettings;
use Silvanite\NovaToolPermissions\NovaToolPermissions;
use Wehaa\CustomLinks\CustomLinks;

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
        ], [], 'mysgs-api');

        NovaSettings::addSettingsFields([
            Number::make('Number of days to consider rules as "New"', 'rule_filter_new_duration')
                ->min(0)->required(),
            Number::make('Number of days to consider rules as "Updated"', 'rule_filter_updated_duration')
                ->min(0)->required(),
        ], [], 'rules');

        NovaSettings::addSettingsFields([
            Text::make('Stage Taxonomy Name', 'stage_taxonomy_name')
                ->required()->default('Stage'),
        ], [], 'jobs');

        Nova::style('sgs', asset('css/nova-sgs.css'));
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
        /*Gate::define('viewNova', function ($user) {
            return Str::endsWith($user->email, '@sgsco.com')
                || Str::endsWith($user->email, '@thr3dcgi.com')
                || $user->hasRoleWithPermission('viewNova');
        });*/
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        $client_cards = [];

        foreach (ClientAccount::all() as $clientAccount) {
            $client_cards[] = new RulesPerWeek($clientAccount);
        }

        return array_merge([
            (new JobsTrend()),
            (new PublishedRules())->width('1/6'),
            (new FlaggedRules)->width('1/6'),
            new RulesPerAccount,
            new NewUsers,
        ],
            $client_cards);
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        $dashboards = [];
        /*foreach(ClientAccount::all() as $clientAccount) {
            $dashboards[] = new ClientDashboard($clientAccount);
        }*/

        return $dashboards;
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        $dev_tools = [];
        $admin_tools = [];

        if (app()->environment() == 'local') {
            $dev_tools[] = new \Cloudstudio\ResourceGenerator\ResourceGenerator();

        }

        if (Gate::check('manageSettings')) {
            $admin_tools[] = new \OptimistDigital\NovaSettings\NovaSettings;
        }

        return array_merge([
            new NovaToolPermissions(),
            new CustomLinks(),
        ], $dev_tools, $admin_tools);
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
