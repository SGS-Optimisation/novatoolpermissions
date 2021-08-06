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
        $count = $request->get('count', 'week');
        $range = $request->get('range', 24);
        $column = $request->get('column', 'created_at');
        $mode = $request->get('mode', 'client');

        $statsBuilder = match($mode){
            'client' => RuleCreationPerClientAccount::class,
            'team' => RuleCreationPerTeam::class
        };

        return Jetstream::inertia()->render($request, 'Stats/ClientAccountStats', [
            'stats' => (new $statsBuilder($count, $range, $column))->handle(),
            'count' => Str::title($count),
            'range' => $range,
            'column' => $column,
            'mode' => $mode,
        ]);
    }
}
