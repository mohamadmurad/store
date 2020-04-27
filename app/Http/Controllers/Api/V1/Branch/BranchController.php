<?php

namespace App\Http\Controllers\Api\V1\Branch;

use App\Http\Controllers\Controller;
use App\branches;
use App\Http\Requests\Branch\StoreBranch;
use App\Http\Requests\Branch\UpdateBranch;
use App\Http\Resources\Branch\BranchResource;
use App\Traits\ApiResponser;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|LengthAwarePaginator
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $branches = Branches::all();
            return $this->showCollection(BranchResource::collection($branches));
        }

        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBranch $request
     * @return BranchResource
     */
    public function store(StoreBranch $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $newBranch = Branches::create($request->only(['name','location','company_id','user_id']));
            return new BranchResource($newBranch);
        }
        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param branches $branch
     * @return BranchResource
     */
    public function show(branches $branch)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new BranchResource($branch);
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
        if (request()->expectsJson() && request()->acceptsJson()){
            $branch->fill($request->only([
                'name',
                'location',
                'balance',
                'user_id',
                'company_id',
            ]));


            if($branch->isClean()){
                return $this->errorResponse([
                    'error'=> 'you need to specify a different value to update',
                    'code'=> 422],
                    422);
            }

            $branch->save();
            return new BranchResource($branch);

        }

        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param branches $branch
     * @return BranchResource
     * @throws Exception
     */
    public function destroy(branches $branch)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {
            $branch->delete();
            return new BranchResource($branch);
        }
        return null;

    }
}
