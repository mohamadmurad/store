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

        if($this->has(['name'])){
            $rules += [
                'name'=>'min:2|max:100',
            ];
        }

        if($this->has(['latinName'])){
            $rules += [
                'latinName'=>'min:2|max:100',
            ];
        }

        if($this->has(['code'])){
            $rules += [
                'code'=>[
                    Rule::unique($this->table,'code')->ignore(request()->segment(3))
                ],
            ];
        }

        if($this->has(['quantity'])){
            $rules += [
                'quantity'=>'min:0|Numeric',
            ];
        }


        if($this->has(['status'])){
            $rules += [
                'status'=>'in:' . Products::AVAILABEL_PRODUCT . ',' . Products::UNAVAILABEL_PRODUCT,
            ];
        }

        if($this->has(['price'])){
            $rules += [
                'price'=>'required|Numeric|min:0',
            ];
        }

        if($this->has(['details'])){
            $rules += [
                'details'=>'string',
            ];
        }



        if($this->has(['parent_id'])){

            if ($this->parentProduct !== 'null') {
                $rules+= [
                    'parent_id'=>'exists:products,id'];
            }

        }

        if($this->has(['category_id'])){
            $rules += [
                'category_id'=>'required|exists:categories,id',
            ];
        }


        if($this->has(['group_id'])){
            if ($this->group !== 'null') {
                $rules += [
                    'group_id'=>'exists:groups,id',
                ];
            }
        }

        return $rules;
    }
}
