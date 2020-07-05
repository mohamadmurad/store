<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class checkUserForBranch
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
        if($request->has('user_id')){
            $user = User::findOrFail($request->user_id)->first();

        }


        return $next($request);
    }
}
