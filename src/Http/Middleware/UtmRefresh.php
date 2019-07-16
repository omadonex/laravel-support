<?php

namespace Omadonex\LaravelSupport\Http\Middleware;

use Closure;

class UtmRefresh
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
        if (!is_null($request->utm_source)) {
            session()->forget(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content']);
            session([
                'utm_source' => $request->utm_source,
                'utm_medium' => $request->utm_medium,
                'utm_campaign' => $request->utm_campaign,
                'utm_content' => $request->utm_content,
            ]);
        }
        return $next($request);
    }
}
