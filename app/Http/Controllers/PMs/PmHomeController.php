<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class PmHomeController extends Controller
{
    public function index(Request $request)
    {

        $myTeams = $request->user()->allTeams()
            ->filter(function ($team) {
            return $team->clientAccount != null;
        });

        foreach($myTeams as $team) {
            $team->clientAccount->loadCount(['rules', 'omnipresent_rules']);
        }

        $otherTeams = Team::with(['clientAccount' => function($query){
            return $query->withCount(['rules', 'omnipresent_rules']);
        }])
            ->whereNotIn('id', $myTeams->pluck('id')->all())
            ->whereHas('clientAccount')
            ->get();

        return Jetstream::inertia()->render($request, 'PM/Landing', [
            'team' => $request->user()->currentTeam,
            'myTeams' => $myTeams,
            'otherTeams' => $otherTeams,
            'clientAccount' => null
        ]);
    }
}
