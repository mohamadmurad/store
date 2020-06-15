<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class checkUserPassword
{

    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $user = Auth::user();

        if(!Hash::check($request->password, $user->password)){
            return $this->errorResponse([
                'message'=> 'your password incorrect',
                'code'=> 422],
                422);
        }
        return $next($request);
    }
}
