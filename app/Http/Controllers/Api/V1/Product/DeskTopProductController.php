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
    use  ApiResponser,UploadAble;

    public function __construct()
    {
        $this->middleware(['permission:show_all_product_info'])->only(['index','show']);
        $this->middleware(['permission:show_product_with_without_sale'])->only(['productWithSale','productWithoutSale']);

       // $this->middleware(['permission:add_product','attributeCheck'])->only('store');
        $this->middleware(['permission:edit_product','attributeCheck'])->only('update');
        $this->middleware(['permission:delete_product'])->only('destroy');

        $this->middleware('checkIfUserHasProduct')->only(['show','update','destroy']);

    }

    /**
 * Display a listing of the resource.
 *
 * @return AnonymousResourceCollection|Response
 */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $employee = Auth::user();
            if ($employee->hasRole('Super Admin')){
                $products = Products::with('attachments')
                    ->with('sales')
                    ->orderBy('created_at','desc')
                    ->get();
                return $this->showCollection(ProductResource::collection($products));
            }
            $employee_branch = Branches::where('user_id',$employee->id)->first();
            $products = Products::where('branch_id',$employee_branch->id)
                ->with('attachments')
                ->with('sales')
                ->orderBy('created_at','desc')
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
        if (request()->expectsJson() && request()->acceptsJson()){

            $employee = Auth::user();
            if ($employee->hasRole('Super Admin')){
                $products = Products::has('sales')
                    ->with('attachments')
                    ->with('sales')
                    ->orderBy('created_at','desc')
                    ->get();
                return $this->showCollection(ProductResource::collection($products));
            }
            $employee_branch = Branches::where('user_id',$employee->id)->first();

            $products = Products::has('sales')->where('branch_id',$employee_branch->id)
                ->with('attachments')
                ->with('sales')
                ->orderBy('created_at','desc')
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
        if (request()->expectsJson() && request()->acceptsJson()){

            $employee = Auth::user();
            if ($employee->hasRole('Super Admin')){
                $products = Products::doesnthave('sales')
                    ->with('attachments')
                    ->orderBy('created_at','desc')
                    ->get();
                return $this->showCollection(ProductResource::collection($products));
            }
            $employee_branch = Branches::where('user_id',$employee->id)->first();

            $products = Products::doesnthave('sales')->where('branch_id',$employee_branch->id)
                ->with('attachments')
                ->orderBy('created_at','desc')
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
    public function store(Request $request)
    {

        return $request->all();

        $saved_files_for_roleBack = [];
        if (request()->expectsJson() && request()->acceptsJson()){
            $user = Auth::user();

            if ($user->hasRole('Super Admin')){
                return $this->errorResponse('Admin cant add product :)',403);
            }

            $branch = $user->branch()->first();

           // DB::beginTransaction();
           // try {

                $newProduct = Products::create([
                    'name' => $request->get('name'),
                    'latinName' => $request->get('latinName'),
                    'code' => $request->get('code'),
                    'quantity' => $request->get('quantity'),
                    'status'=> Products::AVAILABEL_PRODUCT,
                    'price' => $request->get('price'),
                    'details' => $request->get('details'),
                    'parent_id' => $request->get('parent_id') === 'null' ? null : $request->get('parent_id'),
                    'category_id' => $request->get('category')['id'],
                    'group_id' => $request->get('group') === 'null' ? null : $request->get('group')['id'],
                    'branch_id' => $branch->id,
                ]);

                if ($request->has('attributes')){
                    $attributes = $request->get('attributes');

                    foreach ($attributes as $attribute){
                        $newProduct->attributes()->attach($attribute['attribute']['id'], ['value' => $attribute['value']]);
                    }
                }


               /* $AllFiles = $request->file('files');
                foreach ($AllFiles as $file){
                    $attachType = AttachmentType::where('type','like',$file->getMimeType())->first();
                    $saved_file = $this->upload($file,public_path('files/products/'. str_replace(' ','',$branch->name)));
                    $saved_files_for_roleBack += [$saved_file->getFilename()];
                    if ($attachType) {
                        $newAttachment = new Attachment([
                            'src' => str_replace(' ', '', $branch->name) . '/' . $saved_file->getFilename(),
                            'attachmentType_id' => $attachType->id,
                        ]);
                        $newProduct->attachments()->save($newAttachment);
                    }
                }*/

             /*   DB::commit();
            }catch (Exception $e){
                foreach ($saved_files_for_roleBack as $file){
                    File::delete(public_path('files/products'. str_replace(' ','',$branch->name)) . '/' . $file);
                }
                DB::rollBack();




                return $this->errorResponse('Product doesnt added please try again' ,422);


            }*/

         //   dd($newProduct->attachments);

            return new ProductResource($newProduct->load('attachments'));
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

        if (request()->expectsJson() && request()->acceptsJson()){
            $product =$employee_product->load(['sales','attachments']);

            return new ProductResource($product);

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


        if (request()->expectsJson() && request()->acceptsJson()){

            $user = Auth::user();

            if ($user->hasRole('Super Admin')){
                return $this->errorResponse('Admin cant update product :)',403);
            }

            DB::beginTransaction();

            try {

                $employee_product->fill([
                    'name' => $request->has('name') ? $request->get('name') : $employee_product->name,
                    'latinName' => $request->has('latinName') ? $request->get('latinName') : $employee_product->latinName,
                    'code' => $request->has('code') ? $request->get('code') : $employee_product->code,
                    'quantity' => $request->has('quantity') ? $request->get('quantity') : $employee_product->quantity,
                    'status'=> $request->has('status') ? $request->get('status') : $employee_product->status,
                    'price'=> $request->has('price') ? $request->get('price') : $employee_product->price,
                    'details'=> $request->has('details') ? $request->get('details') : $employee_product->details,
                    'parent_id'=> $request->has('parent_id') ? $request->get('parent_id') === 'null' ? null : $request->get('parent_id') : $employee_product->parent_id,
                    'category_id' => $request->has('category_id') ? $request->get('category_id') : $employee_product->category_id,
                    'group_id' => $request->get('group_id') === 'null' ? null : $request->get('group_id'),
                ]);





                if($employee_product->isClean() && ! $request->has('attributes')){

                    return $this->errorResponse([
                        'error'=> 'you need to specify a different value to update',
                        'code'=> 422],
                        422);
                }



                $employee_product->save();

                $attributes = $request->get('attributes');
                foreach ($attributes as $key => $attribute){
                    $employee_product->attributes()->updateExistingPivot($key, ['value' => $attribute]);

                }


                DB::commit();
            }catch (Exception $e){
                DB::rollBack();
                return $this->errorResponse('Product doesnt update please try again' ,422);
            }



            return new ProductResource($employee_product);


        }
        return null;


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
        if (request()->expectsJson() && request()->acceptsJson()){
            DB::beginTransaction();

            try {


                $employee_product->delete();

                DB::commit();
            }catch (Exception $e){
                DB::rollBack();
                return $this->errorResponse('Product doesnt delete please try again' ,422);
            }


            return new ProductResource($employee_product);
        }

        return null;
    }


}
