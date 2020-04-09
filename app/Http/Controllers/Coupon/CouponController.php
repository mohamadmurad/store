<?php

namespace App\Http\Controllers\Coupon;

use App\Coupons;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CouponController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = Coupons::all();
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($coupons);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $code = null;
        $discountRate = null;
        if($request->has('code')){
            $code = $request->get('code');

        }else{
            $code = Coupons::randomCouponCode();

        }
        $rules = [
            'discountRate'=>'required|integer|min:1',
            'code' => 'min:6|max:6',
        ];
        $this->validate($request,$rules);


        $newCoupon = Coupons::create([
            'code' => $code,
            'discountRate' => $request->discountRate,

        ]);

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newCoupon);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Coupons $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupons $coupon)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($coupon);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Coupons $coupon
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Coupons $coupon)
    {
        $rules = [
            'discountRate'=>'required|integer|min:1|max:100',
        ];

        $this->validate($request,$rules);

        $coupon->fill($request->only([
            'discountRate',
        ]));

        if($coupon->isClean()){
            return $this->errorResponse([
                'error'=> 'you need to specify a different value to update',
                'code'=> 422],
                422);
        }

        $coupon->save();
        return $this->showOne($coupon);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Coupons $coupon
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Coupons $coupon)
    {
        $coupon->delete();
        return $this->showOne($coupon);
    }
}
