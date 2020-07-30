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
            'quantity'=>'required|min:1|integer',
            'status'=>'in:' . Products::AVAILABEL_PRODUCT . ',' . Products::UNAVAILABEL_PRODUCT,
            'price'=>'required|Numeric|min:0',
            'details'=>'required|string',
            'category_id'=>'required|exists:categories,id',
            'files'=>'required',
            'files.0'=>'mimeTypes:' . $types,
            'parent_id' => '',
            'group_id' => '',

        ];


        if ($this->has('parent_id')) {
            $rules['parent_id'] .= 'exists:products,id';

        }

        if ($this->has('group_id')) {
            $rules['group_id'] .= 'exists:groups,id';

        }


        return $rules;
    }

}
