<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        $myTeams = $request->user()->allTeams()->filter(function ($team) {
            return $team->clientAccount != null;
        });

        return Jetstream::inertia()->render($request, 'PM/Landing', [
            'team' => $request->user()->currentTeam,
            'myTeams' => $myTeams,
            'clientAccount' => null
        ]);
    }
}
