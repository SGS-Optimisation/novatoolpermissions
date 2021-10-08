<?php

namespace App\Http\Middleware;

use App\Models\ClientAccount;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AutoSwitchTeam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->route('clientAccount')) {
            $slug = $request->route('clientAccount');
            logger('current client account: '.$slug);

            $client = \Cache::remember('client-'.$slug,
                3600,
                function () use ($slug) {
                    return ClientAccount::with('teams')->whereSlug($slug)->first();
                });

            $user = $request->user();

            if(in_array($user->current_team_id, $client->teams->pluck('id')->all())) {
                return $next($request);
            }

            $user_teams = \Cache::remember('user-'.$request->user()->id.'-teams',
                3600,
                function () use ($request) {
                    return $request->user()->teams;
                });

            foreach($client->teams as $team) {
                if(in_array($team->id, $user_teams->pluck('id')->all())) {
                    $user->switchTeam($team);
                }
            }
        }

        return $next($request);
    }
}
