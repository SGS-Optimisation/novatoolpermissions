<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class ValidateSignature
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
        $url = $request->url();

        $queryString = str_replace($request->getPathInfo(), '', $request->getRequestUri());

        $queryString = ltrim(preg_replace('/signature=[^&]+/', '', $queryString), '&');

        $signature = hash_hmac('sha256', trim($url.$queryString, '?'), config('app.key'));

        if (hash_equals($signature, (string) $request->query('signature'))) {
            return $next($request);
        }

        throw new InvalidSignatureException;
    }
}
