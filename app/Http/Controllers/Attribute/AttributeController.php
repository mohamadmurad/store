<?php

namespace App\Http\Controllers\Attribute;

use App\Attributes;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{

    use  ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attributes = Attributes::all();
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($attributes);
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
            'name'=>'required|min:2|max:100|unique:attributes,name',
        ];

        $this->validate($request,$rules);

        $newAttribute = Attributes::create($request->only(['name']));
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newAttribute);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Attributes $attribute
     * @return \Illuminate\Http\Response
     */
    public function show(Attributes $attribute)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($attribute);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Attributes $attribute
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Attributes $attribute)
    {
        $rules = [
            'name'=>['min:2|max:100'
                , Rule::unique($attribute->getTable())->ignore(request()->segment(3))
            ],

        ];

        $this->validate($request,$rules);

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
        return $this->showOne($attribute);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Attributes $attribute
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Attributes $attribute)
    {
        $attribute->delete();
        return $this->showOne($attribute);
    }
}
