<?php

namespace App\Http\Controllers\Category;

use App\Categories;
use App\Http\Controllers\Controller;
use App\Repositories\Category\Interfaces\CategoryRepositoryInterface;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{

    use  ApiResponser;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $cateories = Categories::with('children.children')
            ->whereNull('parent_id')
            ->get();

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($cateories);
        }

       // return view('welcome');

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
            'name'=>'required|min:2|max:100|unique:categories,name',
            'parent_id'=>'required|exists:categories,id',
        ];

        $this->validate($request,$rules);

        if ($request->parent_id === 'null') {
            $request->parent_id = null;
        }

        $newCategory = Categories::create($request->only(['name','parent_id']));

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newCategory);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param Categories $category
     * @return \Illuminate\Http\Response
     */
    public function show(Categories $category)
    {

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($category);
        }

       // return view('welcome');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Categories $category
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Categories $category)
    {

        $rules = [];
        if($request->has(['name'])){
            $rules += [
                'name'=>['min:2|max:100'
                    , Rule::unique($category->getTable())->ignore(request()->segment(3))
                ],

            ];

        }
        if($request->has(['parent_id'])){
            if($request->parent_id === 'null'){
                $request->parent_id = null;

            }else{
                $rules += [
                    'parent_id'=>'required|exists:categories,id',
                ];
            }
        }
        $this->validate($request,$rules);

        $category->fill($request->only([
            'name',
            'parent_id',
        ]));

        if($category->isClean()){
            return $this->errorResponse([
                'error'=> 'you need to specify a different value to update',
                'code'=> 422],
                422);
        }

        $category->save();
        return $this->showOne($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Categories $category
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Categories $category)
    {
        $category->delete();
        return $this->showOne($category);
    }


}
