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

        $this->middleware('checkIfUserHasProduct')->only('show');
        $this->middleware(['permission:add_product','checkIfUserHasProduct'])->only('store');
        $this->middleware(['permission:delete_product','checkIfUserHasProduct'])->only('destroy');
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
    public function store(StoreSale $request,int $employee_product)
    {

       // dd('sd');


        if(request()->expectsJson() && request()->acceptsJson()){
            $product = Products::findOrFail($employee_product);

            if (!$product->sales()->exists()){

                // frmat and validate dates
                $startDate = Carbon::make($request->get('start'));
                $endDate = Carbon::make($request->get('end'));
                if($startDate > $endDate){
                    return $this->errorResponse('End Sale Date must next the Start Date' ,422);
                }

                // calc new price
                $newPrice = 0;
                $saleRate = 0;
                if ($request->has('newPrice')){
                    if ($request->get('newPrice') >= $product->price){
                        return $this->errorResponse('New Price must be less than old' ,422);
                    }

                    $newPrice =  $request->get('newPrice');
                    $saleRate = ($newPrice * 100) / $product->price;
                }elseif ($request->has('saleRate')){

                    $newPrice = ($product->price * (int) $request->get('saleRate')) / 100;
                    $saleRate = $request->get('saleRate');
                }

                $newSale = Sales::create([
                    'product_id' => $employee_product,
                    'saleRate' => $saleRate,
                    'newPrice' => $newPrice,
                    'start' => $startDate,
                    'end' => $endDate,
                ]);


            }else{
                return $this->errorResponse('this product already have sale',422);
            }

            return new SaleResource($newSale);

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
    public function destroy(int $product, int $sale)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
           // dd($sale);
            $sale = Sales::findOrFail($sale);
            $sale_product_id = $sale->product_id;
            if ($product === $sale_product_id){
                $sale->delete();
                return $this->successResponse(['message' => 'sale was deleted.'],200);
            }else{
                $this->errorResponse('this sale not for this product',422);
            }

        }
        return null;
    }
}
