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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class DeskTopProductController extends Controller
{
    use  ApiResponser, UploadAble;

    public function __construct()
    {
        $this->middleware(['permission:show_all_product_info'])->only(['index', 'show']);
        $this->middleware(['permission:show_product_with_without_sale'])->only(['productWithSale', 'productWithoutSale']);

        $this->middleware(['permission:add_product'])->only('store');

        $this->middleware(['attributeCheckForAdd'])->only('addAttributeToProduct');
        $this->middleware(['permission:edit_product', 'attributeCheck'])->only('update');
        $this->middleware(['permission:delete_product'])->only('destroy');

        $this->middleware('checkIfUserHasProduct')->only(['show', 'update', 'destroy']);

    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()) {
            $employee = Auth::user();
            if ($employee->hasRole('Super Admin')) {
                $products = Products::with('attachments')
                    ->with('sales')
                    ->orderBy('created_at', 'desc')
                    ->get();
                return $this->showCollection(ProductResource::collection($products));
            }
            $employee_branch = Branches::where('user_id', $employee->id)->first();
            $products = Products::where('branch_id', $employee_branch->id)
                ->with('attachments')
                ->with('sales')
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->showCollection(ProductResource::collection($products));
        }

        return null;
    }

    /**
     * Display a listing of the product it is saled.
     *
     * @return AnonymousResourceCollection|Response
     */
    public function productWithSale()
    {
        if (request()->expectsJson() && request()->acceptsJson()) {

            $employee = Auth::user();
            if ($employee->hasRole('Super Admin')) {
                $products = Products::has('sales')
                    ->with('attachments')
                    ->with('sales')
                    ->orderBy('created_at', 'desc')
                    ->get();
                return $this->showCollection(ProductResource::collection($products));
            }
            $employee_branch = Branches::where('user_id', $employee->id)->first();

            $products = Products::has('sales')->where('branch_id', $employee_branch->id)
                ->with('attachments')
                ->with('sales')
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->showCollection(ProductResource::collection($products));
        }

        return null;
    }


    /**
     * Display a listing of the product it is not saled.
     *
     * @return AnonymousResourceCollection|Response
     */
    public function productWithoutSale()
    {
        if (request()->expectsJson() && request()->acceptsJson()) {

            $employee = Auth::user();
            if ($employee->hasRole('Super Admin')) {
                $products = Products::doesnthave('sales')
                    ->with('attachments')
                    ->orderBy('created_at', 'desc')
                    ->get();
                return $this->showCollection(ProductResource::collection($products));
            }
            $employee_branch = Branches::where('user_id', $employee->id)->first();

            $products = Products::doesnthave('sales')->where('branch_id', $employee_branch->id)
                ->with('attachments')
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->showCollection(ProductResource::collection($products));
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


        $saved_files_for_roleBack = [];
        if (request()->expectsJson() && request()->acceptsJson()) {
            $user = Auth::user();

            if ($user->hasRole('Super Admin')) {
                return $this->errorResponse('Admin cant add product :)', 403);
            }

            $branch = $user->branch()->first();

            DB::beginTransaction();
            try {

                $newProduct = Products::create([
                    'name' => $request->get('name'),
                    'latinName' => $request->get('latinName'),
                    'code' => $request->get('code'),
                    'quantity' => $request->get('quantity'),
                    'status' => Products::AVAILABEL_PRODUCT,
                    'price' => $request->get('price'),
                    'details' => $request->get('details'),
                    'parent_id' => $request->has('parent_id') ? $request->get('parent_id') : null,
                    'category_id' => $request->get('category_id'),
                    'group_id' => $request->has('group') ? $request->get('group_id') : null,
                    'branch_id' => $branch->id,
                ]);



                $AllFiles = $request->file('files');

                foreach ($AllFiles as $file) {

                    $attachType = AttachmentType::where('type', 'like', $file->getMimeType())->first();
                    $saved_file = $this->upload($file, public_path(config('app.PRODUCTS_FILES_PATH', 'files/products/') . str_replace(' ', '', $branch->name)));
                    $saved_files_for_roleBack += [$saved_file->getFilename()];

                    if ($attachType) {
                        $newAttachment = new Attachment([
                            'src' => str_replace(' ', '', $branch->name) . '/' . $saved_file->getFilename(),
                            'attachmentType_id' => $attachType->id,
                        ]);
                        $newProduct->attachments()->save($newAttachment);
                    }
                }

                DB::commit();
            } catch (Exception $e) {
                foreach ($saved_files_for_roleBack as $file) {
                    File::delete(public_path(config('app.PRODUCTS_FILES_PATH', 'files/products/') . str_replace(' ', '', $branch->name)) . '/' . $file);
                }
                DB::rollBack();

                return  $e;

                return $this->errorResponse('Product doesnt added please try again', 422);


            }

            //return $this->successResponse('hello',200);
            $imageId = AttachmentType::where('type','like','%image%')->pluck('id');
            return new ProductResource($newProduct);
        }

        return null;

    }


    /**
     * addAttributeToProduct
     *
     * @param StoreProduct $request
     * @return ProductResource|JsonResponse
     */
    public function addAttributeToProduct(Request $request, Products $employee_product)
    {

        if (request()->expectsJson() && request()->acceptsJson()) {
            $user = Auth::user();

            if ($user->hasRole('Super Admin')) {
                return $this->errorResponse('Admin cant add product attribute :)', 403);
            }

            $datas = $request->json();
            DB::beginTransaction();
            try {

                foreach ($datas as $data) {

                    if (isset($data['attribute'])) {
                        $attributes = $data['attribute'];
                        $attribute_id = $attributes['id'];
                        $attribute_value = $data['value'];

                        if ($attribute_value === null){
                            return $this->errorResponse('attribute null',422);
                        }

                        if ($attribute_value === ''){
                            return $this->errorResponse('attribute empty',422);
                        }

                        $employee_product->attributes()->attach($attribute_id, ['value' => $attribute_value]);

                    }
                }


                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                return $this->errorResponse('Product doesnt added please try again', 422);


            }

            return new ProductResource($employee_product->load('attachments')->load('attributes'));
        }

        return null;

    }

    /**
     * Display the specified resource.
     *
     * @param $employee_product
     * @return ProductResource
     */
    public function show(Products $employee_product)
    {

        if (request()->expectsJson() && request()->acceptsJson()) {
            $product = $employee_product->load(['sales', 'attachments']);

            //   return new ProductResource($product);
            return $this->showModel(new ProductResource($product));

        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProduct $request
     * @param Products $employee_product
     * @return ProductResource|JsonResponse|Response
     */
    public function update(UpdateProduct $request, Products $employee_product)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {

            $user = Auth::user();

            if ($user->hasRole('Super Admin')) {
                return $this->errorResponse('Admin cant update product :)', 403);
            }

            DB::beginTransaction();

            try {

                if (!isset($request->json()->get('category')['id'])){
                   return $this->errorResponse('category id required',422);
                }

                $employee_product->fill([
                    'name' => $request->has('name') ? $request->get('name') : $employee_product->name,
                    'latinName' => $request->has('latinName') ? $request->get('latinName') : $employee_product->latinName,
                    'code' => $request->has('code') ? $request->get('code') : $employee_product->code,
                    'quantity' => $request->has('quantity') ? $request->get('quantity') : $employee_product->quantity,
                    'status' => $request->has('status') ? $request->get('status') : $employee_product->status,
                    'price' => $request->has('price') ? $request->get('price') : $employee_product->price,
                    'details' => $request->has('details') ? $request->get('details') : $employee_product->details,
                    'parent_id' => $request->has('parent_id') && $request->get('parent_id') > 0 ? $request->get('parent_id') : $employee_product->parent_id,
                    'category_id' => $request->json()->has('category') ? $request->json()->get('category')['id'] : $employee_product->category_id,
                    'group_id' => $request->has('group_id') ? $request->get('group_id') : $employee_product->group_id,
                ]);


                if ($employee_product->isClean() && !$request->has('attributes')) {

                    return $this->errorResponse([
                        'error' => 'you need to specify a different value to update',
                        'code' => 422],
                        422);
                }


                $employee_product->save();
                if ($request->has('attributes')) {
                    $attributes = $request->json()->get('attributes');

                    foreach ($attributes as $attribute) {
                        if (isset($attribute['attribute']['id'])){
                            $id = $attribute['attribute']['id'];
                            if (isset($attribute['value'])){
                                $value = $attribute['value'];
                                $employee_product->attributes()->updateExistingPivot($id, ['value' => $value]);
                            }else{
                                return $this->errorResponse('attribute value required',422);
                            }

                        }else{
                            return $this->errorResponse('attribute id required',422);
                        }


                    }
                }


                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                return $this->errorResponse('Product doesnt update please try again', 422);
            }

            return $this->showModel(new ProductResource($employee_product));


        }

        return 'dfdf';


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Products $product
     * @return ProductResource|JsonResponse
     * @throws Exception
     */
    public function destroy(Products $employee_product)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {
            DB::beginTransaction();

            try {


                $employee_product->delete();

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                return $this->errorResponse('Product doesnt delete please try again', 422);
            }


            //return new ProductResource($employee_product);
            return $this->successResponse([
                'message' => 'product deleted successful',
                'code' => 200,
            ], 200);
        }

        return null;
    }


}
