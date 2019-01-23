<?php

namespace Omadonex\LaravelSupport\Http\Middleware;

use App\User;
use Closure;

class TryAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $apiToken = $request->bearerToken();
        if ($apiToken) {
            $user = User::where('api_token', $apiToken)->first();
            if ($user) {
                auth()->login($user);
            }
        }

        return $next($request);
    }
}
