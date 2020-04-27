<?php

namespace App\Http\Controllers\Api\V1\Card;

use App\Cards;
use App\Http\Controllers\Controller;
use App\Http\Requests\Card\StoreCard;
use App\Http\Requests\Card\UpdateCard;
use App\Http\Resources\Card\CardResource;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class CardController extends Controller
{

    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|LengthAwarePaginator|null
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $cards = Cards::all();
            return $this->showCollection(CardResource::collection($cards));
        }
        return null;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCard $request
     * @return CardResource|Response
     */
    public function store(StoreCard $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $code = Cards::randomCardCode(true);
            $pin = Cards::randomCardPin();

            $newCard = Cards::create([
                'user_id'=> $request->user_id,
                'pin' => $pin,
                'code' => $code,
                'balance' => $request->balance,

            ]);
            return new CardResource($newCard);
        }

        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param Cards $card
     * @return CardResource|Response
     */
    public function show(Cards $card)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new CardResource($card);
        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCard $request
     * @param Cards $card
     * @return CardResource|JsonResponse|Response
     */
    public function update(UpdateCard $request, Cards $card)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $card->fill($request->only([
                'balance',
            ]));

            if($card->isClean()){
                return $this->errorResponse([
                    'error'=> 'you need to specify a different value to update',
                    'code'=> 422],
                    422);
            }

            $card->save();
            return new CardResource($card);
        }
        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Cards $card
     * @return CardResource|Response
     * @throws Exception
     */
    public function destroy(Cards $card)
    {
        if (request()->expectsJson() && request()->acceptsJson()){

            $card->delete();
            return new CardResource($card);
        }
        return null;
    }
}
