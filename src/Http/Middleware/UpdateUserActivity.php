<?php

namespace Omadonex\LaravelSupport\Http\Middleware;

use App\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;

class UpdateUserActivity
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
        if (auth()->check()) {
            DB::table('user_activities')->where('user_id', auth()->id())->update(['last_active_at' => Carbon::now()]);
        }

        return $next($request);
    }
}
