<?php

namespace App\Http\Controllers\Category;

use App\Categories;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategory;
use App\Http\Requests\Category\UpdateCategory;
use App\Http\Resources\Category\CategoryResource;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryController extends Controller
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
            $categories = Categories::with('children')
                ->whereNull('parent_id')
                ->get();
            return $this->showCollection(CategoryResource::collection($categories));
        }

       return null;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCategory $request
     * @return CategoryResource
     */
    public function store(StoreCategory $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $newCategory = Categories::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id === null ? null : $request->parent_id,
            ]);
            return new CategoryResource($newCategory);
        }

        return null;

    }

    /**
     * Display the specified resource.
     *
     * @param Categories $category
     * @return CategoryResource
     */
    public function show(Categories $category)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new CategoryResource($category);
        }

        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCategory $request
     * @param Categories $category
     * @return CategoryResource|JsonResponse|Response
     */
    public function update(UpdateCategory $request, Categories $category)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $category->fill([
                'name' => $request->name,
                'parent_id' => $request->parent_id === null ? null : $request->parent_id,
            ]);

            if($category->isClean()){
                return $this->errorResponse([
                    'error'=> 'you need to specify a different value to update',
                    'code'=> 422],
                    422);
            }

            $category->save();
            return new CategoryResource($category);
        }

        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Categories $category
     * @return CategoryResource|Response
     * @throws Exception
     */
    public function destroy(Categories $category)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $category->delete();
            return new CategoryResource($category);
        }
        return null;
    }


}
