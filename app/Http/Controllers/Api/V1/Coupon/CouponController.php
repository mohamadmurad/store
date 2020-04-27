<?php

namespace App\Http\Controllers\Api\V1\Coupon;

use App\Coupons;
use App\Http\Controllers\Controller;
use App\Http\Requests\Coupon\StoreCoupon;
use App\Http\Requests\Coupon\UpdateCoupon;
use App\Http\Resources\Coupon\CouponResource;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class CouponController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response|LengthAwarePaginator
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $coupons = Coupons::all();
            return $this->showCollection(CouponResource::collection($coupons));
        }
        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCoupon $request
     * @return CouponResource|Response
     */
    public function store(StoreCoupon $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){

            $newCoupon = Coupons::create([
                'code' => $request->code,
                'discountRate' => $request->discountRate,
            ]);

            return new CouponResource($newCoupon);
        }
        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param Coupons $coupon
     * @return CouponResource|Response
     */
    public function show(Coupons $coupon)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new CouponResource($coupon);
        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCoupon $request
     * @param Coupons $coupon
     * @return CouponResource|\Illuminate\Http\JsonResponse|Response
     */
    public function update(UpdateCoupon $request, Coupons $coupon)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
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
            return new CouponResource($coupon);
        }

        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Coupons $coupon
     * @return CouponResource|Response
     * @throws Exception
     */
    public function destroy(Coupons $coupon)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $coupon->delete();
            return new CouponResource($coupon);
        }
        return null;
    }
}
