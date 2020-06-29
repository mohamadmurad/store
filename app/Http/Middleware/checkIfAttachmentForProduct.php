<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use PHPUnit\Framework\MockObject\Api;

class checkIfAttachmentForProduct
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

        if ($request->attachment->product_id === $request->employee_product->id){
            return $next($request);
        }
        return $this->errorResponse('this attachment not for this product',422);

    }
}
