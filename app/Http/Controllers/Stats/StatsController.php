<?php

namespace App\Http\Controllers\Stats;

use App\Features\Stats\JobCreationGlobal;
use App\Features\Stats\JobCreationPerClientAccount;
use App\Features\Stats\RuleCreationPerClientAccount;
use App\Features\Stats\RuleCreationPerRegion;
use App\Features\Stats\RuleCreationPerTeam;
use App\Http\Controllers\Controller;
use App\Models\ClientAccount;
use App\Operations\ClientAccounts\SimplifyAliasesForSearch;
use App\Services\Matomo\Reports\UserVisits;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        if ($client_account_slug) {
            $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        }

        $statsBuilder = match ($level) {
            'client' => RuleCreationPerClientAccount::class,
            'team' => RuleCreationPerTeam::class,
            'region' => RuleCreationPerRegion::class,
        };

        if ($client_account_slug) {
            $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        }

        $stats = (new $statsBuilder(
            view_by: $view_by,
            range: $range,
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

        if ($client_account_slug) {
            $client_account = ClientAccount::whereSlug($client_account_slug)->first();
        }

        $stats = (new $statsBuilder(
            view_by: $view_by,
            range: $range,
            function: $function,
            cumulative: $cumulative,
            column: $column,
            client_account_ids: (isset($client_account) ? $client_account->id : null)
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

    public function visits(Request $request, $client_account_slug = null)
    {
        $grouping =  [
            'User' => 'user',
            'Job Number' => 'job_number',
        ];

        return $this->buildVisitsResponse($request, $client_account_slug, $grouping);
    }


    public function visitsByJobTeam(Request $request, $client_account_slug = null)
    {
        $grouping =  [
            'JobTeam' => 'jobteam',
        ];

        return $this->buildVisitsResponse($request, $client_account_slug, $grouping, 'By JobTeam');
    }

    public function visitsByCountry(Request $request, $client_account_slug = null)
    {
        $grouping =  [
            'Country' => 'country',
        ];

        return $this->buildVisitsResponse($request, $client_account_slug, $grouping, 'By Country');
    }

    protected function buildVisitsResponse(Request $request, $client_account_slug = null, $grouping = null, $name = '')
    {
        $view_by = Str::lower($request->get('visits_view_by', 'range'));

        $date = $request->get('visits_date', [
            Carbon::now()->startOfWeek()->format('Y-m-d'),
            Carbon::now()->startOfWeek()->addDays(6)->format('Y-m-d')
        ]);

        if (is_array($date)) {
            $date = implode(',', $date);
        }

        $level = $request->get('visits_level', 'global');

        if ($slug = $request->get('visits_client_account', $client_account_slug)) {
            $client_account = ClientAccount::whereSlug($slug)->first();

            $client_names = SimplifyAliasesForSearch::handle($client_account);
        }

        $stats = (new UserVisits())->handle($view_by, $date, $slug ? $client_names : null);

        $page_tpl = $client_account_slug ? 'ClientAccount/VisitsStats' : 'Stats/VisitStats';

        return Jetstream::inertia()->render($request, $page_tpl, [
            'stats' => $stats->visits_list,
            'view_by' => Str::title($view_by),
            'date' => $date,
            'level' => $level,
            'clientAccount' => $slug ? $client_account : null,
            'clientAccounts' => ClientAccount::pluck('name', 'slug')->all(),
            'grouping' => $grouping,
            'name' => $name
        ]);
    }
}
