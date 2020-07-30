<?php

namespace App\Http\Middleware;

use App\Attributes;
use App\Products;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\MockObject\Api;
use function MongoDB\BSON\toJSON;

class attributeCheck
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

        if ($request->has('attributes')){
            $attributes = $request->get('attributes');

            $jsonDecode = json_decode($attributes,true);

            foreach ($jsonDecode as $attribute){

                $id = $attribute['attribute']['id'];

                $att = Attributes::findOrFail((int) $id);

                if(count($att->branches()->where('branches_id','=',$branch->id)->get()) == 0){
                    return $this->errorResponse('attribute "' .$att->name  .'" not for this branch',422);
                }
            }
        }

        return $next($request);
    }
}
