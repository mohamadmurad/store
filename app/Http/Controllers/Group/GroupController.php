<?php

namespace App\Http\Controllers\Group;

use App\Groups;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{


    use  ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Groups::all();
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($groups);
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
            'name'=>'required|min:2|max:100|unique:groups,name',
        ];

        $this->validate($request,$rules);

        $newGroup = Groups::create($request->only(['name']));
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newGroup);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Groups $group
     * @return \Illuminate\Http\Response
     */
    public function show(Groups $group)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($group);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Groups $group
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Groups $group)
    {
        $rules = [
            'name'=>['min:2|max:100'
                , Rule::unique($group->getTable())->ignore(request()->segment(3))
            ],

        ];

        $this->validate($request,$rules);

        $group->fill($request->only([
            'name',
        ]));

        if($group->isClean()){
            return $this->errorResponse([
                'error'=> 'you need to specify a different value to update',
                'code'=> 422],
                422);
        }

        $group->save();
        return $this->showOne($group);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Groups $group
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Groups $group)
    {
        $group->delete();
        return $this->showOne($group);
    }
}
