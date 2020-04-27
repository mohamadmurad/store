<?php

namespace App\Http\Requests\Product;

use App\Products;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProduct extends FormRequest
{
    private $table = 'products';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        if($this->has(['title'])){
            $rules += [
                'title'=>'min:2|max:100',
            ];
        }

        if($this->has(['latinTitle'])){
            $rules += [
                'latinTitle'=>'min:2|max:100',
            ];
        }

        if($this->has(['productCode'])){
            $rules += [
                'productCode'=>[
                    Rule::unique($this->table,'code')->ignore(request()->segment(3))
                ],
            ];
        }

        if($this->has(['stock'])){
            $rules += [
                'stock'=>'min:0|Numeric',
            ];
        }


        if($this->has(['situation'])){
            $rules += [
                'situation'=>'in:' . Products::AVAILABEL_PRODUCT . ',' . Products::UNAVAILABEL_PRODUCT,
            ];
        }

        if($this->has(['price'])){
            $rules += [
                'price'=>'required|Numeric|min:0',
            ];
        }

        if($this->has(['description'])){
            $rules += [
                'description'=>'string',
            ];
        }

        if($this->has(['parentProduct'])){

            if ($this->parentProduct !== 'null') {
                $rules+= [
                    'parentProduct'=>'exists:products,id'];
            }

        }

        if($this->has(['category'])){
            $rules += [
                'category'=>'required|exists:categories,id',
            ];
        }


        if($this->has(['group'])){
            if ($this->group !== 'null') {
                $rules += [
                    'group'=>'exists:groups,id',
                ];
            }
        }

        return $rules;
    }


    public function attributes(){
        return [
            'name' => 'title',
            'latinName' => 'latinTitle',
            'code' => 'productCode',
            'quantity' => 'stock',
            'status' => 'situation',
            'price' => 'price',
            'details' => 'description',
            'parent_id' => 'parentProduct',
            'category_id' => 'category',
            'group_id' => 'group',
            'files' => 'media', ///////////////////////////
        ];
    }
}
