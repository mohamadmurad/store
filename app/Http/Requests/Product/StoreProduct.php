<?php

namespace App\Http\Requests\Product;

use App\AttachmentType;
use App\Products;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreProduct extends FormRequest
{
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

        $types = AttachmentType::all()->pluck('type')->join(',');

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
            'files'=>'required',
            'files.0'=>'mimeTypes:' . $types,

        ];


        if ($this->parent_id !== 'null') {
            $rules['parent_id'] .= '|exists:products,id';

        }

        if ($this->group_id !== 'null') {
            $rules['group_id'] .= '|exists:groups,id';

        }

      //  dd($rules);
        return $rules;
    }

}
