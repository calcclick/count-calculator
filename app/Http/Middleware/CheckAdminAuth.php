<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckAdminAuth
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
        if($user = Auth::user()){
            $cRoute = Route::currentRouteName();
            $exceptRoutes = ['unauthorised', 'logout'];

            if($user->user_role !== 'isAdmin'){
                if(!in_array($cRoute, $exceptRoutes)) {
                    return redirect()->route('unauthorised');
                }
            }else{
                if('unauthorised' === Route::currentRouteName()) {
                    return redirect()->route('customerDetails');
                }
            }
        }
        return $next($request);
    }
}
