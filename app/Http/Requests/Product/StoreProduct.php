<?php

namespace App\Http\Requests\Product;

use App\Products;
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
        $rules = [
            'title'=>'required|min:2|max:100',
            'latinTitle'=>'required|min:2|max:100',
            'productCode'=>'required|unique:products,code',
            'stock'=>'required|min:1|integer',
            'situation'=>'in:' . Products::AVAILABEL_PRODUCT . ',' . Products::UNAVAILABEL_PRODUCT,
            'price'=>'required|Numeric|min:0',
            'description'=>'required|string',
            'parentProduct'=>'required',
            'category'=>'required|exists:categories,id',
            'group'=>'required',
            'media'=>'required', ///////////////////////////
        ];
        if ($this->parentProduct !== 'null') {
            $rules['parentProduct'] .= '|exists:products,id';

        }

        if ($this->group !== 'null') {
            $rules['group'] .= '|exists:groups,id';

        }

      //  dd($rules);
        return $rules;
    }


    public function attributes()
    {
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
