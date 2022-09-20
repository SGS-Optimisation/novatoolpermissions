<?php

namespace App\Http\Controllers;

use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Laravel\Jetstream\Jetstream;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function prodSearch(Request $request)
    {
        $invitations = TeamInvitation::with('team.clientAccount')
            ->where('email', $request->user()->email)
            ->get();

        return Jetstream::inertia()->render($request, 'ProdSearch', [
            'team' => optional($request->user())->currentTeam,
            'invitations' => $invitations,
        ]);
    }

    public function pmSearch(Request $request)
    {
        $invitations = TeamInvitation::with('team.clientAccount')
            ->where('email', $request->user()->email)
            ->get();

        return Jetstream::inertia()->render($request, 'PMSearch', [
            'team' => optional($request->user())->currentTeam,
            'invitations' => $invitations,
        ]);
    }
}
