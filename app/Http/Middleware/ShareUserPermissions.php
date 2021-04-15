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
            Inertia::share([
                'user_permissions' => \Cache::remember('user-permissions-'.$request->user()->id,
                    300,
                    function () use ($request) {
                        $accumulator = collect();
                        $request->user()->roles->each(function (Role $role) use ($accumulator) {

                            foreach ($role->permissions as $permission) {
                                $accumulator->add($permission);
                            }
                        });
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
