<?php

namespace App\Http\Controllers\Api\V1\Offer;

use App\Attachment;
use App\AttachmentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Offer\StoreOffer;
use App\Http\Resources\Offer\OfferResource;
use App\Offers;
use App\Products;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use function GuzzleHttp\Promise\all;

class OfferController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->middleware(['permission:add_offer','checkIfUserHasProduct'])->only('store');
        $this->middleware(['checkIfUserHasOffer'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response|LengthAwarePaginator
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $offers = Offers::with('products.firstAttachments')->get();
            return  $this->showCollection(OfferResource::collection($offers));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function store(StoreOffer $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $user = Auth::user();

            if ($user->hasRole('Super Admin')){
                return $this->errorResponse('Admin cant add offer :)',403);
            }

            // format and validate dates
            $startDate = Carbon::make($request->get('start'));
            $endDate = Carbon::make($request->get('end'));
            if($startDate > $endDate){
                return $this->errorResponse('End Offer Date must next the Start Date' ,422);
            }

            if ($startDate < Carbon::today()){
                return $this->errorResponse('Start Date must be newer' ,422);
            }

            $products_ids = $request->products;
            $all_product_price = 0;
            foreach ($products_ids as $product_id){
                $product_price= Products::findOrFail($product_id)->price;
                $all_product_price+=$product_price;
            }
            if ($all_product_price <= $request->price){
                return $this->errorResponse('Offer Price must lower than sum products price ( '. $all_product_price .' )' ,422);
            }

            DB::beginTransaction();
            try {

                $newOffer = Offers::create([
                    'price' => $request->price,
                    'number' => Offers::randomOfferNumber(),
                    'start' => $startDate,
                    'end' => $endDate,
                ]);

                $newOffer->products()->attach($request->get('products'));


                DB::commit();
            }catch (Exception $e){

                DB::rollBack();

                return $this->errorResponse('Offer doesnt added please try again' ,422);
            }

            return $this->successResponse([
                'message' => 'Offer Added successful',
                'code' => 201,
            ],201);



        }
        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param Offers $offer
     * @return OfferResource
     */
    public function show(Offers $offer)
    {
        if (request()->expectsJson() && request()->acceptsJson()){

            return new OfferResource($offer->load('products.firstAttachments'));
        }

        return null;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Offers $offer
     * @return JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Offers $offer)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $offer->delete();
            return $this->successResponse([
                'message' => 'offer deleted successfu',
                'code' => 200,
            ],200);
        }

        return null;
    }
}
