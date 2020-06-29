<?php

namespace App\Http\Controllers\Api\V1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSale;
use App\Http\Resources\product\WebProductResource;
use App\Http\Resources\Sale\SaleResource;
use App\Products;
use App\Sales;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->middleware(['permission:add_sale','checkIfUserHasProduct'])->only('store');
        $this->middleware(['permission:delete_sale','checkIfUserHasProduct'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $products_with_sale  = Products::has('sales')
                ->with('firstAttachments')
                ->with('sales')
                ->orderBy('created_at','desc')
                ->get();

            return $this->showCollection(WebProductResource::collection($products_with_sale));
        }

        return null;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSale $request
     * @param Products $employee_product
     * @return SaleResource|JsonResponse|Response
     */
    public function store(StoreSale $request,Products $employee_product)
    {

        if(request()->expectsJson() && request()->acceptsJson()){



            if (!$employee_product->sales()->exists()){

                // format and validate dates
                $startDate = Carbon::make($request->get('start'));
                $endDate = Carbon::make($request->get('end'));
                if($startDate > $endDate){
                    return $this->errorResponse('End Sale Date must next the Start Date' ,422);
                }

                if ($startDate < Carbon::today()){
                    return $this->errorResponse('Start Date must be newer' ,422);
                }

                // calc new price
                $newPrice = 0;
                $saleRate = 0;
                if ($request->has('newPrice') &&  !$request->has('saleRate')){

                    if ($request->get('newPrice') >= $employee_product->price){
                        return $this->errorResponse('New Price must be less than old' ,422);
                    }

                    $newPrice =  $request->get('newPrice');
                    $saleRate = ($newPrice * 100) / $employee_product->price;
                }elseif ($request->has('saleRate') && !$request->has('newPrice')){
                    $newPrice = ($employee_product->price * (int) $request->get('saleRate')) / 100;
                    $saleRate = $request->get('saleRate');
                }else{

                    $newPrice =  $request->get('newPrice');
                    $saleRate = $request->get('saleRate');
                    $temp_price = ($employee_product->price * (int) $request->get('saleRate')) / 100;
                    if ($newPrice !== $temp_price){
                        return $this->errorResponse('New Price and Sale Rate must be correct' ,422);
                    }
                }


                $employee_product->sales()->create([
                    'saleRate' => $saleRate,
                    'newPrice' => $newPrice,
                    'start' => $startDate,
                    'end' => $endDate,
                ]);


            }else{
                return $this->errorResponse('this product already have sale',422);
            }

            return $this->successResponse([
                'message'=>'New Sale on Product was added successful',
                'code' => 201],201);

        }

        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $product
     * @param int $sale
     * @return SaleResource|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Products $employee_product,Sales $sale)
    {
        if (request()->expectsJson() && request()->acceptsJson()){


            $sale_product_id = $sale->product_id;
            if ($employee_product->id === $sale_product_id){
                $sale->delete();
                return $this->successResponse(['message' => 'sale was deleted.'],200);
            }else{
                $this->errorResponse('this sale not for this product',422);
            }

        }
        return null;
    }
}
