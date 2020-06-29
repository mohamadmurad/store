<?php

namespace App\Http\Controllers\Api\V1\Attribute;

use App\Attributes;
use App\Branches;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attribute\StoreAttribute;
use App\Http\Requests\Attribute\UpdateAttribute;
use App\Http\Resources\Attribute\AttributeResource;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class AttributeController extends Controller
{

    use  ApiResponser;

    public function __construct()
    {
        $this->middleware('permission:add_product|edit_product')->only(['index']);
        $this->middleware('permission:add_attribute')->only(['store']);
        $this->middleware('permission:edit_attribute')->only(['update']);
        $this->middleware('permission:delete_attribute')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|AnonymousResourceCollection|Response|LengthAwarePaginator
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $employee = Auth::user();
            $employee_branch_attribute = Branches::where('user_id',$employee->id)->with('attributes')->first()->attributes;

            if ($employee_branch_attribute->isEmpty()){
                return $this->successResponse([
                    'message' => 'no attribute',
                ],404);
            }else{
                return $this->successResponse([
                    'data' => AttributeResource::collection($employee_branch_attribute)],
                    200);
            }

        }
        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAttribute $request
     * @return AttributeResource|null
     */
    public function store(StoreAttribute $request)
    {


        if (request()->expectsJson() && request()->acceptsJson()){
            $newAttribute = Attributes::create($request->only(['name']));
            return new AttributeResource($newAttribute);
        }

        return null;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAttribute $request
     * @param Attributes $attribute
     * @return AttributeResource|JsonResponse|Response
     */
    public function update(UpdateAttribute $request, Attributes $attribute)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $attribute->fill($request->only([
                'name',
            ]));

            if($attribute->isClean()){
                return $this->errorResponse([
                    'error'=> 'you need to specify a different value to update',
                    'code'=> 422],
                    422);
            }

            $attribute->save();
            return new AttributeResource($attribute);
        }
        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Attributes $attribute
     * @return AttributeResource|null
     * @throws Exception
     */
    public function destroy(Attributes $attribute)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $attribute->delete();
            return new AttributeResource($attribute);
        }

        return null;

    }
}
