<?php

namespace App\Http\Controllers\Api\V1\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSale;
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

class SaleController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $sales = Sales::all();
            return $this->showCollection(SaleResource::collection($sales));
        }

        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return SaleResource|JsonResponse|Response
     */
    public function store(StoreSale $request)
    {
        $fieldName = $request->attributes();
        if(request()->expectsJson() && request()->acceptsJson()){

            $product = Products::findOrFail($request->get($fieldName['product_id']));

            $startDate = Carbon::make($request->get($fieldName['start']));
            $endDate = Carbon::make($request->get($fieldName['end']));

            if($startDate > $endDate){
                return $this->errorResponse('End Sale Date must next the Start Date' ,422);
            }

            $newPrice = ($product->price * (int) $request->get($fieldName['saleRate'])) / 100;
            $newSale = Sales::create([
                'product_id' => $request->get($fieldName['product_id']),
                'saleRate' => $request->get($fieldName['saleRate']),
                'newPrice' => $newPrice,
                'start' => $startDate,
                'end' => $endDate,

            ]);

            return new SaleResource($newSale);

        }

        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Sales $sale
     * @return SaleResource|Response
     * @throws Exception
     */
    public function destroy(Sales $sale)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $sale->delete();
            return new SaleResource($sale);
        }
        return null;
    }
}
