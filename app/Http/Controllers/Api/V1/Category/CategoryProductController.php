<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\AttachmentType;
use App\Categories;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategory;
use App\Http\Requests\Category\UpdateCategory;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\product\WebProductResource;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryProductController extends Controller
{

    use  ApiResponser;


    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|LengthAwarePaginator
     */
    public function index(Categories $category)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $imageId = AttachmentType::where('type','like','%image%')->pluck('id');
            $products = $category->products()->with(['branch','attachments'=> function($query) use ($imageId){
                $query->whereIn('attachmentType_id',$imageId);

            },'sales'])->get();

            return $this->showCollection(WebProductResource::collection($products));
        }

       return null;

    }



}
