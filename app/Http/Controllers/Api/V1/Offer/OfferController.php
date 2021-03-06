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
use function foo\func;
use function GuzzleHttp\Promise\all;

class OfferController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->middleware(['permission:add_offer','checkIfUserHasProduct'])->only('store');
        $this->middleware(['checkIfUserHasOffer'])->only('destroy');
        $this->middleware(['permission:add_offer'])->only('employeeOffer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response|LengthAwarePaginator
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $imageId = AttachmentType::where('type','like','%image%')->pluck('id');
            $offers = Offers::with(['products.attachments'=> function($query) use ($imageId){
                $query->whereIn('attachmentType_id',$imageId);

            },'branch.company'])->get();
            //return  $offers;
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
            $data = $request->json();
            $user = Auth::user();
            $branch_id = $user->branch()->first()->id;
            if ($user->hasRole('Super Admin')){
                return $this->errorResponse('Admin cant add offer :)',403);
            }

            // format and validate dates

            $startDate = Carbon::make($data->get('start'));

            $endDate = Carbon::make($data->get('end'));


            if($startDate > $endDate){
                return $this->errorResponse('End Offer Date must next the Start Date' ,422);
            }

            if ($startDate < Carbon::today()){
                return $this->errorResponse('Start Date must be newer' ,422);
            }

            DB::beginTransaction();
            try {

                $products= $data->has('products') ? $data->get('products') : [];
                $all_product_price = 0;
                $newOffer = Offers::create([
                    'price' => $request->price,
                    'number' => $request->has('number')? $request->get('number') : Offers::randomOfferNumber(),
                    'start' => $startDate,
                    'end' => $endDate,
                    'branch_id'=> $branch_id,
                ]);

                foreach ($products as $product){
                    $product_id = (int)$product['id'];
                    $product_quantity = (int)$product['quantity'];
                    $product_in_db= Products::findOrFail($product_id);
                    $product_price  = $product_in_db->price;
                    if($product_quantity > $product_in_db->quantity){
                        return $this->errorResponse('product ( ' . $product_in_db->name .  ' ) has only '. $product_in_db->quantity .' item' ,422);
                    }
                    $all_product_price+=$product_price;
                    $newOffer->products()->attach($product_id,['quantity'=>$product_quantity]);
                }
                if ($all_product_price <= $request->price){
                    return $this->errorResponse('Offer Price must lower than sum products price ( '. $all_product_price .' )' ,422);
                }






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

            return $this->showModel(new OfferResource($offer->load(['products.attachments','branch'])));
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

    public function employeeOffer(Request $request){


        $employee = Auth::user();
        $employeeBranch = $employee->branch()->first()->offers()->with(['products.attachments','branch'])->get();

        return $this->showCollection(OfferResource::collection($employeeBranch));



    }
}
