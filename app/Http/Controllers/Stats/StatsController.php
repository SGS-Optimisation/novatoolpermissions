<?php

namespace App\Http\Controllers\Stats;

use App\Features\Stats\RuleCreationPerClientAccount;
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
        logger(print_r($request->all(), true));
        $view_by = $request->get('view_by', 'week');
        $range = $request->get('range', 5);
        $column = $request->get('column', 'created_at');
        $level = $request->get('level', 'client');
        $region = $request->get('region', '');
        $function = $request->get('function', 'count');
        $cumulative = $request->get('cumulative', 1);

        logger('cumulative: ' . $cumulative);

        $statsBuilder = match($level){
            'client' => RuleCreationPerClientAccount::class,
            'team' => RuleCreationPerTeam::class
        };

        return Jetstream::inertia()->render($request, 'Stats/ClientAccountStats', [
            'stats' => (new $statsBuilder($view_by, $range, $function, $cumulative, $column))->handle(),
            'view_by' => Str::title($view_by),
            'range' => $range,
            'column' => $column,
            'level' => $level,
            'region' => $region,
            'cumulative' => $cumulative
        ]);
    }
}
