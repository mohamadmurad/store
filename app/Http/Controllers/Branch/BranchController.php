<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\branches;
use App\Traits\ApiResponser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = Branches::all();

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($branches);
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
            'name'=>'required|min:2|max:255|unique:branches,name',
            'location'=>'required|min:2|max:100',
            'company_id'=>'required|integer|exists:companies,id',
            'user_id'=>'required|integer|exists:users,id',
        ];

        $this->validate($request,$rules);


        $newBranch = Branches::create($request->only(['name','location','company_id','user_id']));
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newBranch);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param branches $branch
     * @return \Illuminate\Http\Response
     */
    public function show(branches $branch)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($branch);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param branches $branch
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, branches $branch)
    {
        $rules = [];
        if($request->has(['name'])){
            $rules += [
                'name'=>['min:2|max:100',
                    Rule::unique($branch->getTable())->ignore(request()->segment(3))
                ],

            ];

            $branch->fill($request->only([
                'name',
            ]));

        }
        if($request->has(['location'])){
            $rules += [
                'location'=>['min:2:max:100',
                    Rule::unique($branch->getTable())->ignore(request()->segment(3))
                ],
            ];

            $branch->fill($request->only([
                'location',
            ]));
        }

        if($request->has(['balance'])){
            $rules += [
                'balance'=>'integer|min:0',
            ];

            $branch->fill($request->only([
                'balance',
            ]));
        }

        if($request->has(['user_id'])){
            $rules += [
                'user_id'=>'required|integer|exists:users,id',
            ];

            $branch->fill($request->only([
                'user_id',
            ]));
        }

        $this->validate($request,$rules);



        if($branch->isClean()){
            return $this->errorResponse([
                'error'=> 'you need to specify a different value to update',
                'code'=> 422],
                422);
        }

        $branch->save();
        return $this->showOne($branch);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param branches $branch
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(branches $branch)
    {
        $branch->delete();
        return $this->showOne($branch);
    }
}
