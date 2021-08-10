<?php

namespace App\Http\Controllers\Stats;

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
    public function index(Request $request)
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
            'range' => $range,
            'column' => $column,
            'level' => $level,
            'region' => $region,
            'cumulative' => $cumulative,
            'mode' => 'global',
        ]);
    }
}
