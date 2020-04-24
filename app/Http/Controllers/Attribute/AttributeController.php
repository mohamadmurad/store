<?php

namespace App\Http\Controllers\Attribute;

use App\Attributes;
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

class AttributeController extends Controller
{

    use  ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response|LengthAwarePaginator
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $attributes = Attributes::all();
            return $this->showCollection(AttributeResource::collection($attributes));
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
     * Display the specified resource.
     *
     * @param Attributes $attribute
     * @return AttributeResource|null
     */
    public function show(Attributes $attribute)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new AttributeResource($attribute);
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
