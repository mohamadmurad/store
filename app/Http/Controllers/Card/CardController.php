<?php

namespace App\Http\Controllers\Card;

use App\Cards;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CardController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cards = Cards::all();
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($cards);
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
        $rules = [
            'balance'=>'required|integer|min:0',
            'user_id'=>'required|integer|exists:users,id',
        ];
        $this->validate($request,$rules);
        $code = Cards::randomCardCode(true);
        $pin = Cards::randomCardPin();



        $newCard = Cards::create([
            'user_id'=> $request->user_id,
            'pin' => $pin,
            'code' => $code,
            'balance' => $request->balance,

        ]);

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newCard);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Cards $card
     * @return \Illuminate\Http\Response
     */
    public function show(Cards $card)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($card);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Cards $card
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Cards $card)
    {
        $rules = [
            'balance'=>'required|integer|min:0',
        ];

        $this->validate($request,$rules);

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
        return $this->showOne($card);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Cards $card
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Cards $card)
    {
        $card->delete();
        return $this->showOne($card);
    }
}
