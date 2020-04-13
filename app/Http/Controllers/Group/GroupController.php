<?php

namespace App\Http\Controllers\Group;

use App\Groups;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\StoreGroup;
use App\Http\Requests\Group\UpdateGroup;
use App\Http\Resources\Group\GroupResource;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupController extends Controller
{


    use  ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|LengthAwarePaginator
     */
    public function index()
    {

        if (request()->expectsJson() && request()->acceptsJson()){
            $groups = Groups::all();
            return  $this->showCollection(GroupResource::collection($groups));
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreGroup $request
     * @return GroupResource
     */
    public function store(StoreGroup $request)
    {

        if (request()->expectsJson() && request()->acceptsJson()){
            $newGroup = Groups::create($request->only(['name']));
            return new GroupResource($newGroup);
        }
        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param Groups $group
     * @return GroupResource|null
     */
    public function show(Groups $group)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new GroupResource($group);
        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateGroup $request
     * @param Groups $group
     * @return JsonResponse|void
     */
    public function update(UpdateGroup $request, Groups $group)
    {
        $group->fill($request->only([
            'name',
        ]));

        if($group->isClean()){
            return $this->errorResponse([
                'error'=> 'you need to specify a different value to update',
                'code'=> 422],
                422);
        }
        if (request()->expectsJson() && request()->acceptsJson()) {
            $group->save();
            return $this->showOne($group);
        }
        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Groups $group
     * @return GroupResource
     * @throws Exception
     */
    public function destroy(Groups $group)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {
            $group->delete();
            return new GroupResource($group);
        }
        return null;
    }



}
