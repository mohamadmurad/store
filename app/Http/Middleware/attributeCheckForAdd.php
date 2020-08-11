<?php

namespace App\Http\Middleware;

use App\Attributes;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Support\Facades\Auth;

class attributeCheckForAdd
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
        $branch = $user->branch()->first();
        $datas = $request->json();
        foreach ($datas as $data){

            if (isset($data['attribute'])){
                $attributes = $data['attribute'];
                $attribute_id = $attributes['id'];


                $att = Attributes::findOrFail((int) $attribute_id);
                if(count($att->branches()->where('branches_id','=',$branch->id)->get()) == 0){
                    return $this->errorResponse('attribute "' .$att->name  .'" not for this branch',422);
                }


            }
        }

        return $next($request);
    }
}
