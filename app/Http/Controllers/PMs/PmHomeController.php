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
        })->unique();

        $myClientAccountNames = [];

        foreach($myTeams as $team) {
            $team->clientAccount->loadCount(['rules', 'omnipresent_rules']);
            $myClientAccountNames[] = $team->clientAccount->name;
        }

        $otherTeams = Team::with(['clientAccount' => function($query){
            return $query->withCount(['rules', 'omnipresent_rules']);
        }])
            //->whereNotIn('id', $myTeams->pluck('id')->all())
            ->whereDoesntHave('clientAccount', function($query) use ($myClientAccountNames) {
                return $query->whereIn('name', $myClientAccountNames);
            })
            ->whereHas('clientAccount')
            ->get()
            ->unique('clientAccount.id');

        return Jetstream::inertia()->render($request, 'PM/Landing', [
            'team' => $request->user()->currentTeam,
            'myTeams' => $myTeams,
            'otherTeams' => $otherTeams,
            'clientAccount' => null
        ]);
    }
}
