<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Attachment;
use App\AttachmentType;
use App\Branches;
use App\Categories;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProduct;
use App\Http\Requests\Product\UpdateProduct;
use App\Http\Resources\Category\HomePageResource;
use App\Http\Resources\product\ProductResource;
use App\Http\Resources\product\WebProductResource;
use App\Products;
use App\Traits\ApiResponser;
use App\Traits\UploadAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use function GuzzleHttp\Promise\queue;

class WebProductController extends Controller
{
    use ApiResponser,UploadAble;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $imageId = AttachmentType::where('type','like','%image%')->pluck('id');
            if(\request()->has('sortByPrice')){

                $order = \request()->get('sortByPrice');

                if($order === 'desc' || $order === 'asc'){
                    $products = Products::with(['attachments'=> function($query) use ($imageId){
                        $query->whereIn('attachmentType_id',$imageId);

                    }])->with('sales')->orderBy('price',$order)->get();

                    return $this->showCollection(WebProductResource::collection($products));
                }
            }

            $products = Products::with(['attachments'=> function($query) use ($imageId){
                $query->whereIn('attachmentType_id',$imageId);

            }])->with('sales')->get();



            return $this->showCollection(WebProductResource::collection($products));
        }

        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param Products $product
     * @return WebProductResource|JsonResponse|null
     */
    public function show(Products $product)
    {
        if (request()->expectsJson() && request()->acceptsJson()){

            DB::beginTransaction();
            try {


                $update2 = Products::whereId($product->id)
                    ->where('updated_at', '=', $product->updated_at)
                    ->update(['viewed' => $product->viewed + 1]);


                if (!$update2) {
                    return $this->errorResponse('refresh this page please', 422);
                }
                DB::commit();

                $productNew = Products::whereId($product->id)->with(['sales','attachments','branch.company'])->first();
                return $this->showModel(new WebProductResource($productNew));

            } catch (Exception $e) {
                DB::rollBack();

                return $this->errorResponse('An Error!', 422);
            }

        }
        return null;
    }

    public function homePageContent(Request $request){

        $limit = $request->has('limit') ? $request->get('limit') : 5;

        $rootCategory = Categories::with(['products'=> function($query) use ($limit) {
            $query->orderBy('viewed','desc');
        }])->whereNull('parent_id')->get();

        return $this->showCollection(HomePageResource::collection($rootCategory),false);

    }


}
