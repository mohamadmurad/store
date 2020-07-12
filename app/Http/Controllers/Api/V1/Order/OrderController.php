<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    use ApiResponser;
    public function __construct()
    {
        $this->middleware(['role:customer','checkoutMiddleware'])->only('checkout');
    }


    public function checkout(Request $request){

            $data = $request->json();
            dd($data);

    }
}
