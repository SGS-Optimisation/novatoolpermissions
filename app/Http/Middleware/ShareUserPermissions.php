<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Silvanite\Brandenburg\Role;

class ShareUserPermissions
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
        if (!\Auth::guest()) {

            $current_team = $request->user()->currentTeam;

            Inertia::share([
                'user_permissions' => \Cache::tags('roles')->remember('user-teams-permissions-'.$request->user()->id . '-' . $current_team->id,
                    300,
                    function () use ($request, $current_team) {
                        $accumulator = collect();
                        $request->user()->roles->each(function (Role $role) use ($accumulator) {

                            foreach ($role->permissions as $permission) {
                                $accumulator->add($permission);
                            }
                        });
                        $teamPermissions = $request->user()->teamPermissions($current_team);

                        if(count($teamPermissions) === 1 && $teamPermissions[0] == '*') {
                            $teamPermissions = \Laravel\Jetstream\Jetstream::$permissions;
                        }

                        $accumulator = $accumulator->merge($teamPermissions);
                        //logger($accumulator->join(','));

                        $permissions = $accumulator->unique()->toArray();

                        return array_combine($permissions, array_fill(0, count($permissions), true));
                    }
                ),
            ]);
        }

        return $next($request);
    }
}
