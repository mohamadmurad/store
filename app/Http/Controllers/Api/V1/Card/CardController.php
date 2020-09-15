<?php

namespace App\Http\Controllers\Api\V1\Card;

use App\Deposit;
use App\Cards;
use App\Http\Controllers\Controller;
use App\Http\Requests\Card\SearchByCode;
use App\Http\Requests\Card\StoreCard;
use App\Http\Requests\Card\DepositCardRequest;
use App\Http\Requests\Card\WithdrawRequest;
use App\Http\Resources\Deposit\DepositResource;
use App\Http\Resources\Card\CardResource;
use App\Http\Resources\Withdraw\WithdrawResource;
use App\Traits\ApiResponser;
use App\Withdraw;
use Carbon\Carbon;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use phpseclib\Crypt\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CardController extends Controller
{

    use ApiResponser;

    public function __construct()
    {

        $this->middleware(['permission:deposit'])->only(['deposit']);
        $this->middleware(['permission:withdraw'])->only(['withdraw']);
        $this->middleware(['permission:show_all_deposit'])->only(['allDeposit']);
        $this->middleware(['permission:show_all_withdraw'])->only(['allWithdraw']);

    }


    /**
     * Deposit card
     *
     * @param DepositCardRequest $request
     * @param int $id
     * @return CardResource|JsonResponse|Response
     */
    public function deposit(DepositCardRequest $request, int $id)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {


            $admin = Auth::user();
            DB::beginTransaction();
            try {
                $card = Cards::whereId($id)->lockForUpdate()->first();

                $adminCard = $admin->card()->lockForUpdate()->first();


                $NewBalance = $card->balance + $request->balance;

                $update = Cards::whereId($id)
                    ->where('updated_at', '=', $card->updated_at)
                    ->update(['balance' => $NewBalance]);

                $update = Cards::whereId($adminCard->id)
                    ->where('updated_at', '=', $adminCard->updated_at)
                    ->update(['balance' => $adminCard->balance + $request->cost]);


                if (!$update) {
                    return $this->errorResponse('another transaction work in this card', 422);
                }

                $card->deposit()->attach($admin->id, [
                    'amount' => $request->balance,
                    'cost' => $request->cost,
                    'depositDate' => Carbon::now(),
                ]);

//                $card->fill([
//                    'balance' => $NewBalance,
//                ]);
//
//                if ($card->isClean()) {
//                    return $this->errorResponse([
//                        'error' => 'you need to specify a different value to update',
//                        'code' => 422],
//                        422);
//                }

                //$card->save();


                DB::commit();
            } catch (Exception $e) {

                DB::rollBack();

                return $this->errorResponse('card doesnt charge please try again', 422);
            }


            return $this->successResponse([
                'message' => 'card charged successful',
                'code' => 200,
            ], 200);
        }
        return null;
    }

    /**
     * withdraw card
     *
     * @param DepositCardRequest $request
     * @param Cards $card
     * @return CardResource|JsonResponse|Response
     */
    public function withdraw(WithdrawRequest $request, int $id)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {


            $admin = Auth::user();
            DB::beginTransaction();
            try {
                $card = Cards::whereId($id)->lockForUpdate()->first();
                $adminCard = $admin->card()->lockForUpdate()->first();

                if ($adminCard->balance < $request->balance) {
                    DB::rollBack();
                    return $this->errorResponse('admin not have this balance', 422);
                }

                if ($card->balance < $request->balance) {
                    DB::rollBack();
                    return $this->errorResponse('branch not have this balance', 422);
                }


                $NewBranchBalance = $card->balance - $request->balance;


                $update = Cards::whereId($id)
                    ->where('updated_at', '=', $card->updated_at)
                    ->update(['balance' => $NewBranchBalance]);

                $update = Cards::whereId($adminCard->id)
                    ->where('updated_at', '=', $adminCard->updated_at)
                    ->update(['balance' => $adminCard->balance - $request->balance]);


                if (!$update) {
                    return $this->errorResponse('another transaction work in this card', 422);
                }


                $card->withdraw()->attach($admin->id, [
                    'amount' => $request->balance,
                    'withdrawDate' => Carbon::now(),
                ]);


                DB::commit();
            } catch (Exception $e) {

                DB::rollBack();

                return $this->errorResponse('card doesnt charge please try again', 422);
            }


            return $this->successResponse([
                'message' => 'withdraw successful',
                'code' => 200,
            ], 200);
        }
        return null;
    }


    /**
     * all charge process
     *
     * @return LengthAwarePaginator|null
     */
    public function allDeposit(Request $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {

            $allChargeProcess = Deposit::FilterData($request)->with(['admin', 'card'])->get();

            return $this->showCollection(DepositResource::collection($allChargeProcess));


        }
        return null;
    }


    public function allWithdraw(Request $request)
    {

        $all_withdraw = Withdraw::FilterData($request)->with(['admin', 'card'])->get();

        return $this->showCollection(WithdrawResource::collection($all_withdraw));


    }


    public function getCardByCode(SearchByCode $request){




        $encryptedCode = $request->get('code');

        //dd($encryptedCode);

        $card = Cards::where('code','=',$encryptedCode)->first();
     //   dd($card);
        if ($card){
            return $this->showModel(new CardResource($card));
        }else{
            throw new ModelNotFoundException('Card Not Found');
        }


    }

}
