<?php

namespace App\Http\Middleware;

use App\Offers;
use Closure;
use Illuminate\Support\Facades\Auth;

class checkIfUserHasOffer
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

        if ($request->offer){

            $user = Auth::user();
            $branch_id = $user->branch()->first()->id;
            $offer_branch  =  $request->offer->products[0]->branch_id;

            if ((int)$offer_branch === (int)$branch_id){

                return $next($request);
            }else{

                return $this->errorResponse('You can\'t access this offer',404);
            }


        }
        return $next($request);
    }
}
