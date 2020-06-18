<?php

namespace App\Http\Middleware;

use App\Attributes;
use Closure;

class attributeCheck
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

        $attributes = $request->get('attributes');
        foreach ($attributes as $key => $attribute){
           Attributes::findOrFail((int) $key);
        }
        return $next($request);
    }
}
