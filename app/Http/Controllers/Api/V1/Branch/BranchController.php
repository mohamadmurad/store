<?php

namespace App\Http\Controllers\Api\V1\Branch;

use App\Attributes;
use App\Http\Controllers\Controller;
use App\branches;
use App\Http\Requests\Attribute\SyncAttribute;
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

    public function __construct()
    {
        $this->middleware('permission:add_branch')->only('store');
        $this->middleware('permission:edit_branch')->only('update');
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
        if (request()->expectsJson() && request()->acceptsJson()){
            $branch = Branches::where('user_id','=',$request->user_id)->get();

            if (count($branch) > 0){
                return $this->errorResponse('This User Have a branch',422);
            }else{
                $newBranch = Branches::create($request->only(['name','location','company_id','user_id']));
                return $this->successResponse([
                    'message' => 'Branch added successful',
                    'code'=> 201,
                ],201);
            }

        }
        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param branches $branch
     * @return BranchResource
     */
    /*public function show(branches $branch)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new BranchResource($branch);
        }
        return null;
    }*/

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

            $testBranch = Branches::where('user_id','=',$request->user_id)->where('id','!=',$branch->id)->get();

            if (count($testBranch) > 0){

                return $this->errorResponse('This User Have a branch',422);
            }else{
                $branch->fill([
                    'name' => $request->has('name') ? $request->get('name') : $branch->name,
                    'location' => $request->has('location') ? $request->get('location') : $branch->location,
                    'balance' => $request->has('balance') ? $request->get('balance') : $branch->balance,
                    'user_id' => $request->has('user_id') ? $request->get('user_id') : $branch->user_id,
                    'company_id' => $request->has('company_id') ? $request->get('company_id') : $branch->company_id,
                ]);



                if($branch->isClean()){
                    return $this->errorResponse([
                        'error'=> 'you need to specify a different value to update',
                        'code'=> 422],
                        422);
                }

                $branch->save();
                return $this->successResponse([
                    'message' => 'Branch Updated successful',
                    'code'=> 200,
                ],200);
            }


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
                'message' => 'Branch Deleted successful',
                'code'=> 200,
            ],200);
        }
        return null;

    }


    public function syncAttribute(SyncAttribute $request,Branches $branch){

        $attributesIds = $request->attributesIds;
        foreach ($attributesIds as $attributesId){
            Attributes::findOrFail($attributesId);
        }

        $branch->attributes()->sync($attributesIds,true);


        return $this->successResponse([
            'message' => 'Branch Attribute Sync successful',
            'code' => 200,
        ],200);

    }
}
