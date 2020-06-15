<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class addUser
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
        $roles = $request->roles;
        if ($roles){
            foreach ($roles as $role){
                Role::findById((int) $role,'api');
            }
        }


        $request->password = bcrypt($request->password);

        return $next($request);
    }
}
