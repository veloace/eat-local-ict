<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Administrators
{
    /**
     * Handle an incoming request. Determines if a user is an authorized system admin. IF so, it lets them through.
     * Otherwise it takes them back to the safety of the end-user segment
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(Auth::check())
        {
            if(!Auth::user()->has_world_admin_access)
            {
                return redirect()->route('app');
            }
        }
        return $next($request);
    }
}
