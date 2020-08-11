<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Attachment;
use App\AttachmentType;
use App\Branches;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreAttachment;
use App\Http\Requests\Product\StoreProduct;
use App\Http\Requests\Product\UpdateProduct;
use App\Http\Resources\product\ProductResource;
use App\Http\Resources\product\WebProductResource;
use App\Products;
use App\Traits\ApiResponser;
use App\Traits\checks;
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

class DeskTopProductAttachmentController extends Controller
{
    use  ApiResponser,UploadAble,checks;

    public function __construct()
    {

        $this->middleware(['permission:add_product','checkIfUserHasProduct'])->only('store');
        $this->middleware(['permission:delete_product'])->only('destroy');

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAttachment $request
     * @param Products $employee_product
     * @return ProductResource|JsonResponse
     */
    public function store(StoreAttachment $request , Products $employee_product)
    {

        $saved_files_for_roleBack = [];
        if (request()->expectsJson() && request()->acceptsJson()){
            $user = Auth::user();

            if ($user->hasRole('Super Admin')){
                return $this->errorResponse('Admin cant add attachment :)',403);
            }
            //$AllFiles = $request->file('files');
            $branch = $user->branch()->first();
           // $attachType = AttachmentType::where('type','like',$AllFiles[0]->getMimeType())->first();

          //  dd($attachType->id);

            DB::beginTransaction();
            try {


                $AllFiles = $request->file('files');

                foreach ($AllFiles as $file){
                    $attachType = AttachmentType::where('type','like',$file->getMimeType())->first();
                    $saved_file = $this->upload($file,public_path('files/products/'. str_replace(' ','',$branch->name)));
                    $saved_files_for_roleBack += [$saved_file->getFilename()];


                    if ($attachType){
                        $newAttachment = new Attachment([
                            'src' => str_replace(' ','',$branch->name) . '/' .$saved_file->getFilename(),
                            'attachmentType_id' => $attachType->id,
                        ]);
                        $employee_product->attachments()->save($newAttachment);
                    }

                }

               DB::commit();
            }catch (Exception $e){
                foreach ($saved_files_for_roleBack as $file){
                    File::delete(public_path('files/products'. str_replace(' ','',$branch->name)) . '/' . $file);
                }
               DB::rollBack();



                return $this->errorResponse('Attachment doesnt added please try again' ,422);


            }

            return $this->successResponse(['message'=>'Attachment Added','code'=>201],201);
        }

        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Products $employee_product
     * @param Attachment $attachment
     * @return JsonResponse
     */
    public function destroy(Attachment $attachment)
    {

        if (request()->expectsJson() && request()->acceptsJson()){
            $user = Auth::user();
            $product  = $attachment->products()->withoutGlobalScope('status')->first();

            if (!$this->checkIfUserHasProduct($user,$product)){
                return $this->errorResponse('You can\'t access this product',404);
            }



            DB::beginTransaction();

            try {

               $attachment->delete();

                DB::commit();
            }catch (Exception $e){
                DB::rollBack();
                return $this->errorResponse('Attachment doesnt delete please try again' ,422);
            }


            return $this->successResponse(['message'=>'Attachment deleted','code'=>200],200);
        }

        return null;
    }


}
