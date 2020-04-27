<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Attachment;
use App\AttachmentType;
use App\Branches;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProduct;
use App\Http\Requests\Product\UpdateProduct;
use App\Http\Resources\product\ProductResource;
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

class ProductController extends Controller
{
    use  ApiResponser,UploadAble;
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response
     */
    public function index()
    {

        if (request()->expectsJson() && request()->acceptsJson()){
            $products = Products::all();
            return ProductResource::collection($products);
        }

        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProduct $request
     * @return ProductResource|JsonResponse
     */
    public function store(StoreProduct $request)
    {
        $fieldName = $request->attributes();
        $saved_files_for_roleBack = [];
        if (request()->expectsJson() && request()->acceptsJson()){
            $user = Branches::first()->pluck('user_id');
            $branch = Branches::where('user_id' ,'=', $user)->pluck('id');
            DB::beginTransaction();
            try {
                $newProduct = Products::create([

                    'name' => $request->get($fieldName['name']),
                    'latinName' => $request->get($fieldName['latinName']),
                    'code' => $request->get($fieldName['code']),
                    'quantity' => $request->get($fieldName['quantity']),
                    'status'=> Products::AVAILABEL_PRODUCT,
                    'price' => $request->get($fieldName['price']),
                    'details' => $request->get($fieldName['details']),
                    'parent_id' => $request->get($fieldName['parent_id']) === 'null' ? null : $request->get($fieldName['parent_id']),
                    'category_id' => $request->get($fieldName['category_id']),
                    'group_id' => $request->get($fieldName['group_id']) === 'null' ? null : $request->get($fieldName['group_id']),
                    'branch_id' => $branch[0],
                ]);

                $AllFiles = $request->file($fieldName['files']);
                foreach ($AllFiles as $file){
                    $saved_file = $this->upload($file,public_path('files/products'));
                    $saved_files_for_roleBack += [$saved_file->getFilename()];
                    $newAttachment = new Attachment([
                        'src' => $saved_file->getFilename(),
                        'attachmentType_id' => 1,
                    ]);
                    $newProduct->attachments()->save($newAttachment);
                }

                DB::commit();
            }catch (Exception $e){
                foreach ($saved_files_for_roleBack as $file){
                    File::delete(public_path('files/products') . '/' . $file);
                }
                DB::rollBack();



                return $this->errorResponse('Product doesnt added please try again' ,422);


            }

            return new ProductResource($newProduct);
        }

        return null;

    }

    /**
     * Display the specified resource.
     *
     * @param Products $product
     * @return ProductResource|Response
     */
    public function show(Products $product)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new ProductResource($product);
        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProduct $request
     * @param Products $product
     * @return ProductResource|JsonResponse|Response
     */
    public function update(UpdateProduct $request, Products $product)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $fieldName = $request->attributes();


            $product->fill([
                'name' => $request->has($fieldName['name']) ? $request->get($fieldName['name']) : $product->name,
                'latinName' => $request->has($fieldName['latinName']) ? $request->get($fieldName['latinName']) : $product->latinName,
                'code' => $request->has($fieldName['code']) ? $request->get($fieldName['code']) : $product->code,
                'quantity' => $request->has($fieldName['quantity']) ? $request->get($fieldName['quantity']) : $product->quantity,
                'status'=> $request->has($fieldName['status']) ? $request->get($fieldName['status']) : $product->status,
                'price'=> $request->has($fieldName['price']) ? $request->get($fieldName['price']) : $product->price,
                'details'=> $request->has($fieldName['details']) ? $request->get($fieldName['details']) : $product->details,
                'parent_id'=> $request->has($fieldName['parent_id']) ? $request->get($fieldName['parent_id']) === 'null' ? null : $request->get($fieldName['parent_id']) : $product->parent_id,
                'category_id' => $request->get($fieldName['category_id']),
                'group_id' => $request->get($fieldName['group_id']) === 'null' ? null : $request->get($fieldName['group_id']),
            ]);



            if($product->isClean()){
                return $this->errorResponse([
                    'error'=> 'you need to specify a different value to update',
                    'code'=> 422],
                    422);
            }

            $product->save();

            return new ProductResource($product);


        }
        return null;


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Products $product
     * @return ProductResource|Response
     * @throws Exception
     */
    public function destroy(Products $product)
    {

        if (request()->expectsJson() && request()->acceptsJson()){
            $product->delete();
            return new ProductResource($product);
        }

        return null;
    }


}
