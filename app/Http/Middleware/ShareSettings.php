<?php

namespace App\Http\Middleware;

use App\Models\ClientAccount;
use App\Models\Taxonomy;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!\Auth::guest()) {
            Inertia::share([
                'settings' => [
                    'rule_filter_new_duration' => nova_get_setting('rule_filter_new_duration'),
                    'rule_filter_updated_duration' => nova_get_setting('rule_filter_updated_duration'),
                    'show_flagged_rules_content' => nova_get_setting('show_flagged_rules_content'),
                ],
                'features' => [
                    'matomo_tracking_enabled' => nova_get_setting('matomo_tracking_enabled'),
                    'matomo_host' => nova_get_setting('matomo_host'),
                    'matomo_site_id' => nova_get_setting('matomo_site_id'),

                    'allow_force_account' => nova_get_setting('allow_force_account'),
                ],
                'loader_messages' => config('loader'),

                'client_accounts' => \Cache::tags(['client-accounts'])
                    ->remember('all-client-accounts', 3600, function (){
                        return ClientAccount::pluck('name', 'slug');
                    }),

                'all_job_stages' => \Cache::tags(['taxonomy'])
                    ->remember('all-job-stages', 3600, function (){
                        return Taxonomy::whereName(nova_get_setting('stage_taxonomy_name', 'Stage'))
                            ->first()->terms->pluck('name')->all();
                    })
            ]);
        }
        return $next($request);
    }
}
