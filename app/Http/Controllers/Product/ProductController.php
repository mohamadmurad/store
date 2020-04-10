<?php

namespace App\Http\Controllers\Product;

use App\Attachment;
use App\Branches;
use App\Http\Controllers\Controller;
use App\Products;
use App\Traits\ApiResponser;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    use  ApiResponser,UploadAble;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Products::all();

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($products);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $user = Branches::first()->pluck('user_id');
        $branch = Branches::where('user_id' ,'=', $user)->pluck('id');

        $rules = [
            'name'=>'required|min:2|max:100',
            'latinName'=>'required|min:2|max:100',
            'code'=>'required|unique:products,code',
            'quantity'=>'required|min:1|integer',
            'status'=>'in:' . Products::AVAILABEL_PRODUCT . ',' . Products::UNAVAILABEL_PRODUCT,
            'price'=>'required|Numeric|min:0',
            'details'=>'required|string',
            'parent_id'=>'required',
            'category_id'=>'required|exists:categories,id',
            'group_id'=>'required',
            'files'=>'required', ///////////////////////////
        ];

        $this->validate($request,$rules);
        $rules = [];
        if ($request->parent_id === 'null') {
            $request->parent_id = null;
        }else{
            $rules += [
                'parent_id'=>'exists:products,id',
            ];
        }

        if ($request->group_id === 'null') {
            $request->group_id = null;
        }else{
            $rules += [
                'group_id'=>'exists:groups,id',
            ];
        }
        $this->validate($request,$rules);

        $saved_files_for_roleBack = [];
        DB::beginTransaction();
        try {
            $newProduct = Products::create([
                'name' => $request->get('name'),
                'latinName' => $request->get('latinName'),
                'code' => $request->get('code'),
                'quantity' => $request->get('quantity'),
                'status'=> Products::AVAILABEL_PRODUCT,
                'price' => $request->get('price'),
                'details' => $request->get('details'),
                'parent_id' => $request->get('parent_id') === 'null' ? null : $request->get('parent_id'),
                'category_id' => $request->get('category_id'),
                'group_id' => $request->get('group_id') === 'null' ? null : $request->get('group_id'),
                'branch_id' => $branch[0],
            ]);

            $AllFiles = $request->file('files');
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
        }catch (\Exception $e){

            DB::rollBack();
            foreach ($saved_files_for_roleBack as $file){
                $dsd = File::delete(public_path('files/products') . '/' . $file);

            }

            if (request()->expectsJson() && request()->acceptsJson()){
                return $this->errorResponse('Product doesnt added please try again' ,422);
            }

        }

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newProduct);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Products $product
     * @return \Illuminate\Http\Response
     */
    public function show(Products $product)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($product);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Products $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Products $product
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Products $product)
    {
        $product->delete();
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($product);
        }
    }


}
