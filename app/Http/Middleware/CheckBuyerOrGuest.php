<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckBuyerOrGuest
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
        if (Auth::check() && Auth::user()->role === User::BUYER) {
            return $next($request);
        }

        if (!Auth::check()) {
            return $next($request);
        }
        abort(404);
        // return redirect(route('admin.dashboard'));
    }
}