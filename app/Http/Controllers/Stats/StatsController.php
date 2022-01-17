<?php

namespace App\Http\Controllers\Stats;

use App\Features\Stats\JobCreationGlobal;
use App\Features\Stats\JobCreationPerClientAccount;
use App\Features\Stats\RuleCreationPerClientAccount;
use App\Features\Stats\RuleCreationPerRegion;
use App\Features\Stats\RuleCreationPerTeam;
use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;

class StatsController extends Controller
{
    public function rules(Request $request, $client_account_slug = null)
    {
        $view_by = $request->get('rules_view_by', 'week');
        $range = $request->get('rules_range', 5);
        $function = $request->get('rules_function', 'count');
        $cumulative = $request->get('rules_cumulative', 1);
        $region = $request->get('rules_region', '');
        $column = $request->get('rules_column', 'created_at');
        $level = $request->get('rules_level', 'client');

        if($client_account_slug) {
            $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        }

        $statsBuilder = match ($level) {
            'client' => RuleCreationPerClientAccount::class,
            'team' => RuleCreationPerTeam::class,
            'region' => RuleCreationPerRegion::class,
        };

        if($client_account_slug) {
            $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        }

        $stats = (new $statsBuilder(
            view_by: $view_by,
            range:$range,
            function: $function,
            cumulative: $cumulative,
            region: $region,
            column: $column,
            client_account_ids: ($client_account->id ?? null)
        ))->handle();

        $page_tpl = isset($client_account) ? 'ClientAccount/RuleStats' : 'Stats/RuleStats';

        return Jetstream::inertia()->render($request, $page_tpl, [
            'stats' => $stats,
            'view_by' => Str::title($view_by),
            'range' => (int) $range,
            'column' => $column,
            'level' => $level,
            'region' => $region,
            'cumulative' => (int) $cumulative,
            'mode' => 'global',
            'clientAccount' => $client_account ?? null,
        ]);
    }

    public function jobs(Request $request, $client_account_slug = null)
    {
        $view_by = $request->get('jobs_view_by', 'week');
        $range = $request->get('jobs_range', 15);
        $column = $request->get('jobs_column', 'created_at');
        $level = $request->get('jobs_level', 'client');
        $function = $request->get('jobs_function', 'count');
        $cumulative = $request->get('jobs_cumulative', 0);

        $statsBuilder = match ($level) {
            'client' => JobCreationPerClientAccount::class,
            'global' => JobCreationGlobal::class,
        };

        if($client_account_slug) {
            $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        }

        $stats = (new $statsBuilder(
            view_by: $view_by,
            range: $range,
            function: $function,
            cumulative: $cumulative,
            column: $column,
            client_account_ids: ($client_account->id ?? null)
        ))->handle();

        $page_tpl = isset($client_account) ? 'ClientAccount/JobStats' : 'Stats/JobStats';

        return Jetstream::inertia()->render($request, $page_tpl, [
            'stats' => $stats,
            'view_by' => Str::title($view_by),
            'range' => (int) $range,
            'column' => $column,
            'level' => $level,
            'cumulative' => (int) $cumulative,
            'mode' => 'global',
            'clientAccount' => $client_account ?? null,
        ]);
    }
}
