<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;

class PmHomeController extends Controller
{
    public function index(Request $request)
    {
        $myTeams = $request->user()->allTeams()
            ->filter(function ($team) {
                return $team->clientAccount != null;
            })->unique();

        $myClientAccountNames = [];

        foreach ($myTeams as $team) {
            $team->clientAccount->loadCount(['rules', 'omnipresent_rules']);
            $myClientAccountNames[] = $team->clientAccount->name;
        }

        $invitations = TeamInvitation::with('team.clientAccount')
            ->where('email', $request->user()->email)
            ->get();

        foreach ($invitations as $invitation) {
            if (!in_array($invitation->team->clientAccount->name, $myClientAccountNames)) {
                $invitation->team->clientAccount->loadCount(['rules', 'omnipresent_rules']);
                $myClientAccountNames[] = $invitation->team->clientAccount->name;
            }
        }

        $otherTeams = Team::with([
            'clientAccount' => function ($query) {
                return $query->withCount(['rules', 'omnipresent_rules']);
            }
        ])
            //->whereNotIn('id', $myTeams->pluck('id')->all())
            ->whereDoesntHave('clientAccount', function ($query) use ($myClientAccountNames) {
                return $query->whereIn('name', $myClientAccountNames);
            })
            ->whereHas('clientAccount')
            ->get()
            ->unique('clientAccount.id');

        return [
            'myTeams' => $myTeams,
            'otherTeams' => $otherTeams,
            'invitations' => $invitations,
        ];
    }
}
