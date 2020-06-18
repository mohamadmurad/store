<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Attachment;
use App\AttachmentType;
use App\Branches;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProduct;
use App\Http\Requests\Product\UpdateProduct;
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

class WebProductController extends Controller
{
    use ApiResponser,UploadAble;
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $products = Products::with('firstAttachments')->with('sales')->get();
            return $this->showCollection(WebProductResource::collection($products));
        }

        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param Products $product
     * @return WebProductResource|null
     */
    public function show(Products $product)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $product = $product->load(['sales','attachments']);
            return new WebProductResource($product);
        }
        return null;
    }


}
