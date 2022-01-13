<?php

namespace App\Http\Controllers\Stats;

use App\Features\Stats\JobCreationGlobal;
use App\Features\Stats\JobCreationPerClientAccount;
use App\Features\Stats\RuleCreationPerClientAccount;
use App\Features\Stats\RuleCreationPerRegion;
use App\Features\Stats\RuleCreationPerTeam;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;

class StatsController extends Controller
{
    public function rules(Request $request)
    {
        $view_by = $request->get('view_by', 'week');
        $range = $request->get('range', 5);
        $column = $request->get('column', 'created_at');
        $level = $request->get('level', 'client');
        $region = $request->get('region', '');
        $function = $request->get('function', 'count');
        $cumulative = $request->get('cumulative', 1);

        $statsBuilder = match ($level) {
            'client' => RuleCreationPerClientAccount::class,
            'team' => RuleCreationPerTeam::class,
            'region' => RuleCreationPerRegion::class,
        };

        $stats = (new $statsBuilder(
            view_by: $request->get('view_by', 'week'),
            range: $request->get('range', 5),
            function: $request->get('function', 'count'),
            cumulative: $request->get('cumulative', 1),
            region: $request->get('region', ''),
            column: $request->get('column', 'created_at')
        ))->handle();

        return Jetstream::inertia()->render($request, 'Stats/ClientAccountStats', [
            'stats' => $stats,
            'view_by' => Str::title($view_by),
            'range' => (int) $range,
            'column' => $column,
            'level' => $level,
            'region' => $region,
            'cumulative' => (int) $cumulative,
            'mode' => 'global',
        ]);
    }

    public function jobs(Request $request)
    {
        $view_by = $request->get('view_by', 'week');
        $range = $request->get('range', 15);
        $column = $request->get('column', 'created_at');
        $level = $request->get('level', 'client');
        $function = $request->get('function', 'count');
        $cumulative = $request->get('cumulative', 0);

        $statsBuilder = match ($level) {
            'client' => JobCreationPerClientAccount::class,
            'global' => JobCreationGlobal::class,
        };

        $stats = (new $statsBuilder(
            view_by: $view_by,
            range: $range,
            function: $function,
            cumulative: $cumulative,
            column: $column
        ))->handle();

        return Jetstream::inertia()->render($request, 'Stats/JobStats', [
            'stats' => $stats,
            'view_by' => Str::title($view_by),
            'range' => (int) $range,
            'column' => $column,
            'level' => $level,
            'cumulative' => (int) $cumulative,
            'mode' => 'global',
        ]);
    }
}
