<?php

namespace App\Http\Controllers\Api\V1\Branch;

use App\Attributes;
use App\Cards;
use App\Http\Controllers\Controller;
use App\branches;
use App\Http\Requests\Attribute\SyncAttribute;
use App\Http\Requests\Branch\StoreBranch;
use App\Http\Requests\Branch\UpdateBranch;
use App\Http\Resources\Branch\BranchReceivablesResource;
use App\Http\Resources\Branch\BranchResource;
use App\Traits\ApiResponser;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{

    use ApiResponser;

    public function __construct()
    {
        $this->middleware(['permission:add_branch', 'checkUserForBranch'])->only('store');
        $this->middleware(['role:Super Admin', 'checkUserForBranch'])->only('update');
        $this->middleware(['permission:edit_branch', 'checkUserForBranch'])->only('updateByEmployee');
        $this->middleware('permission:delete_branch')->only('destroy');

        $this->middleware(['role:Super Admin'])->only('syncAttributes');
    }
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|LengthAwarePaginator
     */
    /* public function index()
     {
         if (request()->expectsJson() && request()->acceptsJson()){
             $branches = Branches::all();
             return $this->showCollection(BranchResource::collection($branches));
         }

         return null;
     }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBranch $request
     * @return BranchResource|JsonResponse
     */
    public function store(StoreBranch $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {
            $branch = Branches::where('user_id', '=', $request->user_id)->get();

            if (count($branch) > 0) {
                return $this->errorResponse(trans('error.userHaveBranch'), 422);
            } else {
                $newBranch = Branches::create($request->only(['name', 'location', 'phone', 'company_id', 'user_id']));
                return $this->successResponse([
                    'message' => trans('success.branch.added'),
                    'code' => 201,
                ], 201);
            }

        }
        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param branches $branch
     * @return JsonResponse
     */
    public function show(branches $branch)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {
            return $this->showModel(new BranchResource($branch));
        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBranch $request
     * @param branches $branch
     * @return BranchResource|JsonResponse|void
     */
    public function update(UpdateBranch $request, branches $branch)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {

            $testBranch = Branches::where('user_id', '=', $request->user_id)->where('id', '!=', $branch->id)->get();

            if (count($testBranch) > 0) {
                return $this->errorResponse(trans('error.userHaveBranch'), 422);
            } else {
                $branch->fill([
                    'name' => $request->has('name') ? $request->get('name') : $branch->name,
                    'location' => $request->has('location') ? $request->get('location') : $branch->location,
                    'phone' => $request->has('phone') ? $request->get('phone') : $branch->phone,
                    'user_id' => $request->has('user_id') ? $request->get('user_id') : $branch->user_id,
                    'company_id' => $request->has('company_id') ? $request->get('company_id') : $branch->company_id,
                ]);


                if ($branch->isClean()) {
                    return $this->errorResponse([
                        'error' => trans('error.update_specify'),
                        'code' => 422],
                        422);
                }

                $branch->save();
                return $this->successResponse([
                    'message' => trans('success.branch.updated'),
                    'code' => 200,
                ], 200);
            }


        }

        return null;

    }


    public function updateByEmployee(UpdateBranch $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {

            $user = Auth::user();
            $branch = $user->branch()->first();

            $branch->fill([
                'name' => $request->has('name') ? $request->get('name') : $branch->name,
                'location' => $request->has('location') ? $request->get('location') : $branch->location,
                'phone' => $request->has('phone') ? $request->get('phone') : $branch->phone,
            ]);


            if ($branch->isClean()) {
                return $this->errorResponse([
                    'error' => trans('error.update_specify'),
                    'code' => 422],
                    422);
            }

            $branch->save();
            return $this->successResponse([
                'message' => trans('success.branch.updated'),
                'code' => 200,
            ], 200);
        }


        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param branches $branch
     * @return BranchResource|JsonResponse
     * @throws Exception
     */
    public function destroy(branches $branch)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {
            $branch->delete();
            return $this->successResponse([
                'message' => trans('success.branch.deleted'),
                'code' => 200,
            ], 200);
        }
        return null;

    }


    public function syncAttribute(SyncAttribute $request, Branches $branch)
    {

        $attributesIds = $request->attributesIds;
        foreach ($attributesIds as $attributesId) {
            Attributes::findOrFail($attributesId);
        }

        $branch->attributes()->sync($attributesIds, true);


        return $this->successResponse([
            'message' => trans('success.branch.attributeSync'),
            'code' => 200,
        ], 200);

    }


    public function receivablesToPay()
    {


        $receivables = Branches::join('cards', function ($join) {
            $join->on('branches.user_id', '=', 'cards.user_id')->where('cards.balance', '>', 0);
        })->with(['company', 'employee'])->get();


        return $this->showCollection(BranchReceivablesResource::collection($receivables));

    }
}
