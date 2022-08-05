<?php

namespace App\Http\Controllers\PMs;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class PmHomeController extends Controller
{
    public function index(Request $request)
    {
        return Jetstream::inertia()->render($request, 'PM/Landing', [
            'team' => $request->user()->currentTeam,
            'clientAccount' => null,
        ]);
    }
}
