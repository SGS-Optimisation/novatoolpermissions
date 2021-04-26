<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareSettings
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
                'settings' => [
                    'rule_filter_new_duration' => nova_get_setting('rule_filter_new_duration'),
                    'rule_filter_updated_duration' => nova_get_setting('rule_filter_updated_duration'),
                ]
            ]);
        }
        return $next($request);
    }
}
